#!/bin/bash

# 1. Viajar automáticamente a la carpeta del proyecto en la Chromebook
# (Asumiendo que lo guardarás en la carpeta Documentos de la laptop)
cd "$HOME/Documents/postreskg" || { echo "No se pudo encontrar la carpeta del proyecto. Asegúrate de que esté en Documents/postreskg"; exit 1; }

# 2. Levantar el servidor interno ultra-ligero de PHP en el puerto 8000
php artisan serve --port=8000 > /dev/null 2>&1 &

# 3. Guardar el ID del proceso del servidor para poder apagarlo al salir
SERVER_PID=$!

# 4. Esperar un segundo a que el servidor responda
sleep 1

# 5. Abrir el navegador web predeterminado directo en el inventario
xdg-open "http://127.0.0.1:8000/productos"

# 6. Mantener el script alerta: si se cierra la terminal o el proceso, apaga el servidor
trap "kill $SERVER_PID" EXIT
wait