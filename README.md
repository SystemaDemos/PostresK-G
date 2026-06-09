# PostresKG — Sistema de Punto de Venta (POS) Comercial 🛒

Aplicación de Punto de Venta (POS) desarrollada para la gestión comercial y control de inventario en tiempo real. El núcleo del proyecto fue diseñado con un enfoque estricto en la optimización de consultas y ligereza, permitiendo su ejecución fluida como entorno local en equipos de recursos limitados.

## 🚀 Características Principales
* **Gestión de Inventario:** Control de stock, registro de productos y alertas automáticas.
* **Módulo de Ventas:** Procesamiento rápido de transacciones y cálculo automatizado de totales.
* **Autenticación y Seguridad:** Manejo seguro de sesiones y control de acceso.
* **Rendimiento Optimizado:** Arquitectura de base de datos eficiente para garantizar consultas rápidas sin sobrecargar el procesador ni la memoria local.

## 🛠️ Stack Técnico
* **Backend:** PHP / Framework Laravel
* **Base de Datos:** SQLite / MySQL (Optimizado para transacciones locales rápidas)
* **Frontend:** HTML5, CSS3 estructurado bajo la metodología **BEM** (Block, Element, Modifier) para un diseño modular y mantenible.
* **Despliegue/Entorno:** Configurado para entornos de bajo consumo (Sistemas Linux ligeros).

---

## 💻 Instalación en Local

1. Clonar el repositorio:
   ```bash
   git clone [https://github.com/SystemaDemos/PostresKG.git](https://github.com/SystemaDemos/PostresKG.git)

2. Instalar dependencias de PHP:
    composer install

3. Configurar el archivo de entorno:
    cp .env.example .env
    php artisan key:generate

4. Ejecutar migraciones:
    php artisan migrate

5. Iniciar servidor local:
    php artisan serve
