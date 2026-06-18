<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MikroTikController extends Controller
{
    private $socket;
    private string $host     = '';
    private int    $port     = 8728;
    private string $usuario  = '';
    private string $password = '';

    private function cargarConexion(int $idSucursal): bool
    {
        $mdPath = base_path('mikrotik.md');
        if (!file_exists($mdPath)) return false;

        $sections = preg_split('/^## /m', file_get_contents($mdPath));

        foreach ($sections as $section) {
            if (!preg_match('/ID_SUCURSAL:\s*(\d+)/i', $section, $m)) continue;
            if ((int)$m[1] !== $idSucursal) continue;

            preg_match('/Host:\s*(.+)/i',                $section, $host);
            preg_match('/Usuario:\s*(.+)/i',             $section, $user);
            preg_match('/Contraseña:\s*(.+)/i',          $section, $pass);
            preg_match('/Puerto API MikroTik:\s*(\d+)/i', $section, $port);

            $this->host     = trim($host[1] ?? '');
            $this->usuario  = trim($user[1] ?? '');
            $this->password = trim($pass[1] ?? '');
            $this->port     = (int)($port[1] ?? 8728);

            return $this->host !== '';
        }

        return false;
    }

    private function connect(): bool
    {
        $this->socket = @fsockopen($this->host, $this->port, $errno, $errstr, 4);
        if (!$this->socket) return false;
        stream_set_timeout($this->socket, 4);
        $this->send(['/login', "=name={$this->usuario}", "=password={$this->password}"]);
        $r = $this->readAll();
        return isset($r[0][0]) && $r[0][0] === '!done';
    }

    private function encodeLen(int $len): string
    {
        if ($len < 0x80)     return chr($len);
        if ($len < 0x4000)   return pack('n', $len | 0x8000);
        return pack('N', $len | 0xC0000000);
    }

    private function readLen(): int
    {
        $b = ord(fread($this->socket, 1));
        if ($b < 0x80) return $b;
        if ($b < 0xC0) return (($b & 0x3F) << 8) | ord(fread($this->socket, 1));
        if ($b < 0xE0) { $r = fread($this->socket, 2); return (($b & 0x1F) << 16) | (ord($r[0]) << 8) | ord($r[1]); }
        $r = fread($this->socket, 3); return (($b & 0x0F) << 24) | (ord($r[0]) << 16) | (ord($r[1]) << 8) | ord($r[2]);
    }

    private function send(array $words): void
    {
        foreach ($words as $w) fwrite($this->socket, $this->encodeLen(strlen($w)) . $w);
        fwrite($this->socket, chr(0));
    }

    private function readSentence(): array
    {
        $sentence = [];
        while (true) {
            $len = $this->readLen();
            if ($len === 0) break;
            $sentence[] = fread($this->socket, $len);
        }
        return $sentence;
    }

    private function readAll(): array
    {
        $result = [];
        while (true) {
            $s = $this->readSentence();
            if (empty($s)) break;
            $result[] = $s;
            if (in_array($s[0], ['!done', '!fatal', '!trap'])) break;
        }
        return $result;
    }

    private function command(string $cmd, array $args = []): array
    {
        $words = [$cmd];
        foreach ($args as $k => $v) $words[] = "?$k=$v";
        $this->send($words);
        $rows = [];
        foreach ($this->readAll() as $sent) {
            if ($sent[0] !== '!re') continue;
            $row = [];
            foreach (array_slice($sent, 1) as $word) {
                if (str_starts_with($word, '=')) {
                    [$k, $v] = explode('=', substr($word, 1), 2) + ['', ''];
                    $row[$k] = $v;
                }
            }
            $rows[] = $row;
        }
        return $rows;
    }

    private function disconnect(): void
    {
        if ($this->socket) fclose($this->socket);
    }

    private function pingIP(string $ip): bool
    {
        try {
            $this->send(['/tool/ping', '=address=' . $ip, '=count=1', '=interval=0.5']);
            foreach ($this->readAll() as $sent) {
                foreach ($sent as $word) {
                    if (str_starts_with($word, '=received=') && (int)substr($word, 10) > 0) return true;
                    if (str_starts_with($word, '=time=')) return true;
                }
            }
        } catch (\Throwable) {}
        return false;
    }

    public function enlaces()
    {
        $equipos = \DB::table('infra_movimientos_productos as m')
            ->join('infra_sucursal as s',       's.ID_SUCURSAL',  '=', 'm.ID_SUCURSAL')
            ->join('infra_producto as p',        'p.ID_PRODUCTO',  '=', 'm.ID_PRODUCTO')
            ->join('infra_tipo_producto as t',   't.ID_TIPO_PRO',  '=', 'p.ID_TIPO_PRO')
            ->join('infra_marca_producto as ma', 'ma.ID_MARCA',    '=', 'p.ID_MARCA')
            ->join('infra_estado as e',          'e.ID_ESTADO',    '=', 'm.ID_ESTADO')
            ->whereRaw('LOWER(t.NOMBRE_TIPO) LIKE ?', ['%router%'])
            ->whereRaw('LOWER(ma.DESCRIPCION_MARCA) LIKE ?', ['%cisco%'])
            ->whereRaw('LOWER(p.MODELO_PRO) LIKE ?', ['%modem%'])
            ->select(
                'm.ID_MOVIMIENTO', 'm.IP_INTERNA', 'm.NSERIE_PRO', 'm.UBICACION_PRO',
                'p.MODELO_PRO', 't.NOMBRE_TIPO', 'ma.DESCRIPCION_MARCA',
                'e.NOMBRE_ESTADO', 's.NOMBRE_SUCURSAL'
            )
            ->get();

        try {
            $this->cargarConexion(10);
            if (!$this->connect()) throw new \Exception('Sin conexión al router');

            $resultado = $equipos->map(function ($eq) {
                $ip     = trim($eq->IP_INTERNA ?? '');
                $activo = $ip ? $this->pingIP($ip) : false;
                return [
                    'id'        => $eq->ID_MOVIMIENTO,
                    'producto'  => "{$eq->NOMBRE_TIPO} · {$eq->DESCRIPCION_MARCA} · {$eq->MODELO_PRO}",
                    'serie'     => $eq->NSERIE_PRO   ?? '—',
                    'ip'        => $ip ?: '—',
                    'ubicacion' => $eq->UBICACION_PRO ?? '—',
                    'sede'      => $eq->NOMBRE_SUCURSAL,
                    'estado_inv'=> $eq->NOMBRE_ESTADO,
                    'activo'    => $activo,
                ];
            });

            $this->disconnect();

            return response()->json([
                'total'   => $resultado->count(),
                'activos' => $resultado->where('activo', true)->count(),
                'enlaces' => $resultado->sortByDesc('activo')->values(),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 503);
        }
    }

    public function redSucursal(Request $request)
    {
        $idSucursal = $request->input('id_sucursal', 10);

        // Solo equipos vigentes con IP asignada en esa sucursal
        $equipos = \DB::table('infra_movimientos_productos as m')
            ->join('infra_sucursal as s',  's.ID_SUCURSAL',  '=', 'm.ID_SUCURSAL')
            ->join('infra_producto as p',  'p.ID_PRODUCTO',  '=', 'm.ID_PRODUCTO')
            ->join('infra_tipo_producto as t', 't.ID_TIPO_PRO', '=', 'p.ID_TIPO_PRO')
            ->join('infra_marca_producto as ma', 'ma.ID_MARCA', '=', 'p.ID_MARCA')
            ->join('infra_estado as e', 'e.ID_ESTADO', '=', 'm.ID_ESTADO')
            ->where('m.ID_SUCURSAL', $idSucursal)
            ->where('m.VIGENTE', 1)
            ->whereNotNull('m.IP_INTERNA')
            ->where('m.IP_INTERNA', '!=', '')
            ->select(
                'm.ID_MOVIMIENTO',
                'm.IP_INTERNA',
                'm.NSERIE_PRO',
                'm.UBICACION_PRO',
                'p.MODELO_PRO',
                't.NOMBRE_TIPO',
                'ma.DESCRIPCION_MARCA',
                'e.NOMBRE_ESTADO',
                's.NOMBRE_SUCURSAL'
            )
            ->get();

        if ($equipos->isEmpty()) {
            return response()->json(['sucursal' => '', 'equipos' => [], 'arp' => []]);
        }

        $sucursalNombre = $equipos->first()->NOMBRE_SUCURSAL;

        try {
            if (!$this->cargarConexion((int)$idSucursal)) throw new \Exception("Sede #{$idSucursal} no está configurada en mikrotik.md");
            if (!$this->connect()) throw new \Exception('Sin conexión al router');

            $arpRows  = $this->command('/ip/arp/print');
            $addrRows = $this->command('/ip/address/print');

            // IPs propias del router → siempre activas
            $ipsRouter = collect($addrRows)
                ->map(fn($r) => explode('/', $r['address'] ?? '')[0])
                ->flip()->all();

            // IPs en ARP con estado válido (excluye failed e incomplete)
            $ipsArp = collect($arpRows)
                ->whereNotIn('status', ['failed', 'incomplete'])
                ->pluck('address')->flip()->all();

            $ipsArp = array_merge($ipsArp, $ipsRouter);

            // Para equipos NO detectados por ARP, verificar con ping
            $resultado = $equipos->map(function ($eq) use ($ipsArp) {
                $ip  = trim($eq->IP_INTERNA);
                $via = 'none';

                if (isset($ipsArp[$ip])) {
                    $via = 'arp';
                } elseif ($ip && $this->pingIP($ip)) {
                    $via = 'ping';
                }

                return [
                    'id'         => $eq->ID_MOVIMIENTO,
                    'producto'   => "{$eq->NOMBRE_TIPO} · {$eq->DESCRIPCION_MARCA} · {$eq->MODELO_PRO}",
                    'serie'      => $eq->NSERIE_PRO ?? '—',
                    'ip'         => $ip,
                    'ubicacion'  => $eq->UBICACION_PRO ?? '—',
                    'estado_inv' => $eq->NOMBRE_ESTADO,
                    'activo_red' => $via !== 'none',
                    'via'        => $via,
                ];
            });

            $this->disconnect();

        } catch (\Throwable $e) {
            $this->disconnect();
            $resultado = $equipos->map(fn($eq) => [
                'id'         => $eq->ID_MOVIMIENTO,
                'producto'   => "{$eq->NOMBRE_TIPO} · {$eq->DESCRIPCION_MARCA} · {$eq->MODELO_PRO}",
                'serie'      => $eq->NSERIE_PRO ?? '—',
                'ip'         => trim($eq->IP_INTERNA),
                'ubicacion'  => $eq->UBICACION_PRO ?? '—',
                'estado_inv' => $eq->NOMBRE_ESTADO,
                'activo_red' => false,
                'via'        => 'none',
            ]);
        }

        return response()->json([
            'sucursal' => $sucursalNombre,
            'total'    => $resultado->count(),
            'activos'  => $resultado->where('activo_red', true)->count(),
            'equipos'  => $resultado->values(),
        ]);
    }

    public function status()
    {
        try {
            $this->cargarConexion(10);
            if (!$this->connect()) return response()->json(['error' => 'No se pudo conectar'], 503);

            $sucursal = \DB::table('infra_sucursal')->where('ID_SUCURSAL', 10)->value('NOMBRE_SUCURSAL') ?? 'MikroTik';

            $res   = $this->command('/system/resource/print')[0] ?? [];
            $iface = $this->command('/interface/print');
            $ips   = $this->command('/ip/address/print');
            $rutas = $this->command('/ip/route/print');

            $this->disconnect();

            // Convertir uptime legible
            $uptime = $res['uptime'] ?? '—';

            // RAM %
            $ramTotal = (int)($res['total-memory'] ?? 0);
            $ramLibre = (int)($res['free-memory'] ?? 0);
            $ramUsada = $ramTotal - $ramLibre;
            $ramPct   = $ramTotal > 0 ? round($ramUsada / $ramTotal * 100) : 0;

            // HDD %
            $hddTotal = (int)($res['total-hdd-space'] ?? 0);
            $hddLibre = (int)($res['free-hdd-space'] ?? 0);
            $hddPct   = $hddTotal > 0 ? round(($hddTotal - $hddLibre) / $hddTotal * 100) : 0;

            return response()->json([
                'sucursal' => $sucursal,
                'sistema' => [
                    'nombre'    => $res['board-name'] ?? '—',
                    'version'   => $res['version'] ?? '—',
                    'uptime'    => $uptime,
                    'cpu'       => $res['cpu-load'] ?? '0',
                    'cpu_model' => $res['cpu'] ?? '—',
                    'ram_pct'   => $ramPct,
                    'ram_mb'    => round($ramUsada / 1024 / 1024),
                    'ram_total' => round($ramTotal / 1024 / 1024),
                    'hdd_pct'   => $hddPct,
                    'hdd_mb'    => round(($hddTotal - $hddLibre) / 1024 / 1024),
                    'hdd_total' => round($hddTotal / 1024 / 1024),
                ],
                'interfaces' => array_map(fn($i) => [
                    'nombre'  => $i['name'] ?? '—',
                    'tipo'    => $i['type'] ?? '—',
                    'estado'  => ($i['running'] ?? 'false') === 'true' ? 'up' : 'down',
                    'mac'     => $i['mac-address'] ?? '—',
                    'comment' => $i['comment'] ?? '',
                ], $iface),
                'ips' => array_map(fn($ip) => [
                    'direccion'  => $ip['address'] ?? '—',
                    'interfaz'   => $ip['interface'] ?? '—',
                    'red'        => $ip['network'] ?? '—',
                    'comentario' => $ip['comment'] ?? '',
                ], $ips),
                'rutas' => array_map(fn($r) => [
                    'destino'  => $r['dst-address'] ?? '—',
                    'gateway'  => $r['gateway'] ?? '—',
                    'distancia'=> $r['distance'] ?? '—',
                    'activa'   => ($r['active'] ?? 'false') === 'true',
                ], $rutas),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
