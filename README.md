# Enterprise Invoicing, Inventory & POS Core Engine 💼⚡

[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com/)
[![Database](https://img.shields.io/badge/Database-PostgreSQL%20%7C%20MySQL-4169E1?style=for-the-badge&logo=postgresql&logoColor=white)](https://www.postgresql.org/)
[![Architecture](https://img.shields.io/badge/Architecture-Clean%20%2F%20Domain--Driven-009688?style=for-the-badge)](https://en.wikipedia.org/wiki/Clean_architecture)

Motor backend empresarial para la emisión de **Facturación Electrónica, Gestión de Inventario con Kardex Inmutable y Control de Cajas (POS)**. Diseñado con altos estándares de concurrencia, integridad referencial estricta y desacoplamiento por capas.

Inspirado en las arquitecturas abiertas de **[Crater Invoice](https://github.com/crater-invoice/crater)**, **[Invoice Ninja](https://github.com/invoiceninja/invoiceninja)** y **[Akaunting](https://github.com/akaunting/akaunting)**.

---

## 🏛️ Puntos Clave de Arquitectura & Base de Datos

1. **Multi-Sucursal & Multi-Almacén:**
   - Soporte nativo para empresas con múltiples sucursales físicas, puntos de venta independientes y almacenes descentralizados.
2. **Kardex Inmutable (Audit-Trail):**
   - Tabla `stock_movements` de solo inserción (*Append-Only*). Calcula saldos en tiempo real mediante el método de **Costo Promedio Ponderado**.
3. **Manejo de Concurrencia en Facturación:**
   - Control de correlativos (`document_series`) preparado para incrementos atómicos con `lockForUpdate()`, evitando duplicidad en emisión de comprobantes fiscales (Facturas `01`, Boletas `03`, Notas `07`/`08`).
4. **Protección Atómica de Turnos de Caja (POS):**
   - Restricción única inteligente `UNIQUE(cash_register_id, open_flag)` para impedir la apertura simultánea de dos turnos en la misma caja registradora tanto en PostgreSQL como en MySQL.

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

## 🗄️ Mapeo de Migraciones

| Migración | Módulo | Descripción |
| :--- | :--- | :--- |
| `000001_create_tenancy_tables` | Tenancy & Estructura | Empresas, sucursales y almacenes con auditoría y soft deletes. |
| `000002_create_contacts_tables` | Directorio Fiscal | Clientes y proveedores con identificación tributaria y direcciones. |
| `000003_create_catalog_tables` | Catálogo de Productos | Categorías, impuestos, unidades, productos y variantes SKU. |
| `000004_create_inventory_tables` | Inventario & Kardex | Stocks por almacén y movimientos inmutables con costo ponderado. |
| `000005_create_cash_tables` | Punto de Venta & Caja | Cajas, métodos de pago, sesiones/turnos y arqueo detallado. |
| `000006_create_sales_tables` | Ventas & Facturación | Series de documentos, cotizaciones, facturas, ítems y cobros. |
| `000007_create_purchases_tables` | Compras & Abastecimiento | Órdenes de compra a proveedores con impacto automático a inventario. |

---

## 🚀 Tecnologías & Estándares
- **PHP 8.2+** / **Laravel 11**
- **Strict Typing & Form Requests**
- **Clean Architecture & Service Layer**
- **PostgreSQL / MySQL Compatibility**
