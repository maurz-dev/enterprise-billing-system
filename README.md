# Enterprise Invoicing, Inventory & POS Core Engine 💼⚡

[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com/)
[![Database](https://img.shields.io/badge/Database-PostgreSQL%20%7C%20MySQL-4169E1?style=for-the-badge&logo=postgresql&logoColor=white)](https://www.postgresql.org/)
[![Docker](https://img.shields.io/badge/Docker-Enabled-2496ED?style=for-the-badge&logo=docker&logoColor=white)](https://www.docker.com/)
[![Architecture](https://img.shields.io/badge/Architecture-Clean%20%2F%20Domain--Driven-009688?style=for-the-badge)](https://en.wikipedia.org/wiki/Clean_architecture)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg?style=for-the-badge)](https://opensource.org/licenses/MIT)

Motor backend empresarial para la emisión de **Facturación Electrónica, Gestión de Inventario con Kardex Inmutable y Control de Cajas (Punto de Venta)**. Diseñado bajo estándares de alta concurrencia, tipado estricto con PHP 8.2+ y desacoplamiento por capas.

Inspirado en la arquitectura abierta y mejores prácticas de proyectos líderes como **[Crater Invoice](https://github.com/crater-invoice/crater)**, **[Invoice Ninja](https://github.com/invoiceninja/invoiceninja)** y **[Akaunting](https://github.com/akaunting/akaunting)**.

---

## 🏛️ Características de Arquitectura & Diseño de Datos

1. **Multi-Tenancy y Multi-Sucursal:**
   - Estructura jerárquica: `Company` ➔ `Branch` ➔ `Warehouse` / `CashRegister`.
   - Soporte para múltiples puntos de emisión y almacenes descentralizados.
2. **Kardex Inmutable (Audit-Trail):**
   - Tabla `stock_movements` de solo inserción (*Append-Only*). Cada fila registra el costo promedio ponderado y balance resultante con enlaces polimórficos (`Invoice`, `Purchase`, etc.).
3. **Manejo de Concurrencia en Facturación:**
   - Series de documentos (`document_series`) con tipificación fiscal (`01` Factura, `03` Boleta, `07` NC, `08` ND) preparadas para incrementos atómicos con `lockForUpdate()`.
4. **Protección Atómica en Turnos de Caja (POS):**
   - Restricción `UNIQUE(cash_register_id, open_flag)`: `open_flag` es `true` mientras el turno está activo y pasa a `NULL` al cerrar. Esto previene aperturas simultáneas sin fallar en PostgreSQL ni MySQL.
5. **Modelos Eloquent con Tipado Estricto (PHP 8.2 Backed Enums):**
   - Uso de Enums tipados para estados de facturas (`InvoiceStatus`), tipos de documentos (`DocType`), motivos de kardex (`StockMovementReason`) y métodos de pago (`PaymentMethodType`).

---

## 📂 Estructura del Proyecto

```text
├── app/
│   ├── Enums/                 # PHP 8.2 Backed Enums (DocType, InvoiceStatus, etc.)
│   └── Models/                # 23 Modelos Eloquent con relaciones, casts y scopes
├── bootstrap/
│   └── app.php                # Configuración modular moderna de Laravel 11
├── database/
│   └── migrations/            # 7 migraciones completas con constraints relacionales
├── docker/
│   └── nginx/conf.d/app.conf  # Servidor web Nginx optimizado
├── docs/
│   └── er-diagram.md          # Diagrama ER completo
├── routes/
│   └── api.php                # Endpoints REST organizados por dominios
├── composer.json              # Dependencias de producción y desarrollo
├── docker-compose.yml         # Entorno completo (PHP-FPM, Nginx, PostgreSQL, Redis)
└── Dockerfile                 # Contenedor Alpine liviano con extensiones PHP
```

---

## 📊 Diagrama Entidad-Relación (ERD)

```mermaid
erDiagram
    companies ||--o{ branches : tiene
    branches ||--o{ warehouses : tiene
    branches ||--o{ cash_registers : tiene
    branches ||--o{ document_series : emite
    companies ||--o{ users : emplea
    companies ||--o{ contacts : registra
    contacts ||--o{ contact_addresses : tiene
    companies ||--o{ products : cataloga
    categories ||--o{ products : agrupa
    units ||--o{ products : mide
    taxes ||--o{ products : grava
    products ||--|{ product_variants : tiene
    product_variants ||--o{ stocks : saldo
    warehouses ||--o{ stocks : almacena
    product_variants ||--o{ stock_movements : kardex
    warehouses ||--o{ stock_movements : kardex
    contacts ||--o{ quotes : recibe
    quotes ||--|{ quote_items : contiene
    quotes ||--o| invoices : se_convierte_en
    contacts ||--o{ invoices : recibe
    invoices ||--|{ invoice_items : contiene
    invoices ||--o{ invoice_payments : cobra
    invoices ||--o{ invoices : nota_credito_debito
    product_variants ||--o{ invoice_items : vende
    contacts ||--o{ purchases : provee
    purchases ||--|{ purchase_items : contiene
    payment_methods ||--o{ invoice_payments : usa
    cash_registers ||--o{ cash_sessions : turnos
    cash_sessions ||--o{ cash_movements : libro
    cash_sessions ||--o{ cash_session_counts : arqueo
    cash_sessions ||--o{ invoice_payments : recibe
    payment_methods ||--o{ cash_movements : clasifica
```

---

## 🚀 Despliegue Local con Docker

Para levantar todo el entorno de desarrollo (PHP 8.2, Nginx, PostgreSQL 15 y Redis):

```bash
# 1. Clonar el repositorio
git clone https://github.com/maurz-dev/enterprise-billing-system.git
cd enterprise-billing-system

# 2. Configurar variables de entorno
cp .env.example .env

# 3. Levantar servicios con Docker Compose
docker compose up -d --build

# 4. Ejecutar migraciones
docker compose exec app php artisan migrate
```

---

## 👨‍💻 Autor
Desarrollado por **Maurice Chavez ([@maurz-dev](https://github.com/maurz-dev))**
