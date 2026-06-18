# Proyecto MikroTik

## FARMACIA COMUNAL SUR
- ID_SUCURSAL: 10
- Host: 186.10.251.22
- Usuario: supervisor
- Contraseña: asus2025
- Puerto SSH: 2222
- Puerto API MikroTik: 8728 ✅ (método activo)

## CESFAM SUR
- ID_SUCURSAL:4
- Host: 164.77.124.182
- Usuario: claude
- Contraseña: pluton2026
- Puerto SSH: 2222
- Puerto API MikroTik: 8728 ✅ (método activo)

## CASA CENTRAL
- ID_SUCURSAL:22
- Host: 192.168.1.5
- Usuario: claude
- Contraseña: pluton2026
- Puerto SSH: 2222
- Puerto API MikroTik: 8728 ✅ (método activo)

## Instrucciones para Claude
- Antes de hacer cualquier cambio, muéstrame qué vas a ejecutar
- No apliques nada sin mi confirmación
- Si algo puede afectar la conectividad, avísame primero
- Trabaja con el archivo backup-config.rsc que está en esta carpeta

## Contexto del router
- Router: MikroTik RB3011UiAS
- RouterOS: 7.20 (stable) — build 2025-09-29
- CPU: ARM 2 núcleos 1400MHz
- RAM: 1024 MB
- Arquitectura: arm
- Red local: 10.1.32.1/24

## Monitoreo
- Panel integrado en el sistema inventario-ti: /mikrotik
- Conexión vía API MikroTik (puerto 8728) — pura PHP sockets
- Auto-refresco cada 10 segundos
- Muestra: CPU, RAM, interfaces, IPs, rutas
