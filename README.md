# Confecciones Soledad - Sistema de Gestión

**Sistema integral de gestión para taller de confección y arreglos de prendas**

[![Laravel](https://img.shields.io/badge/Laravel-11-red.svg)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3-green.svg)](https://vuejs.org)
[![Inertia.js](https://img.shields.io/badge/Inertia.js-1.0-purple.svg)](https://inertiajs.com)
[![License](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)

---

## � Tabla de Contenidos

- [Características](#características)
- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Configuración](#configuración)
- [Uso](#uso)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Testing](#testing)
- [Despliegue](#despliegue)
- [Soporte](#soporte)

---

## ✨ Características

### 🎨 **Interfaz Pública Moderna**
- Landing page con gradientes y animaciones
- Catálogo de trabajos realizados (antes/después)
- Formulario de solicitud de pedidos (sin login requerido)
- Página "Nosotros" con información del negocio

### 👤 **Panel de Cliente**
- Dashboard con estadísticas personales
- Visualización de pedidos propios
- Historial detallado

### 🛠️ **Panel Administrativo Completo**
1. **Gestión de Usuarios** - 3 roles (Propietario, Ayudante, Cliente)
2. **Gestión de Portafolio** - Trabajos con imágenes antes/después
3. **Gestión de Pedidos** - Estados, presupuestos, fechas
4. **Gestión de Inventario** - Stock y movimientos
5. **Gestión de Promociones** - Descuentos porcentuales y fijos
6. **Gestión de Reseñas** - Calificaciones de clientes
7. **Pagos Electrónicos** - **Integración PagoFácil QR**
8. **Reportes y Estadísticas** - Ingresos, costos, rentabilidad

### 🎨 **Sistema de Temas**
- **3 Temas**: Niños, Jóvenes, Adultos
- **Modo Automático Día/Noche** (6am-6pm / 6pm-6am)
- **Accesibilidad**: Tamaño de texto y alto contraste

### 📊 **Contador de Visitas**
- Seguimiento por página
- Mostrado en footer

---

## � Requisitos

### Software Necesario
- **PHP** ≥ 8.2
- **Composer** ≥ 2.0
- **Node.js** ≥ 18.x
- **npm** ≥ 9.x
- **PostgreSQL** ≥ 14

### Extensiones de PHP
- BCMath
- Ctype
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO (pgsql)
- Tokenizer
- XML

---

## 📦 Instalación

### 1. Clonar el Repositorio
```bash
git clone https://github.com/tu-usuario/confecciones-soledad.git
cd confecciones-soledad
```

### 2. Instalar Dependencias de PHP
```bash
composer install
```

### 3. Instalar Dependencias de Node.js
```bash
npm install --legacy-peer-deps
```

### 4. Configurar Variables de Entorno
```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configurar Base de Datos

Edita `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=confecciones_soledad
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

### 6. Ejecutar Migraciones
```bash
php artisan migrate
```

### 7. Poblar Base de Datos (Opcional)
```bash
php artisan db:seed --class=PortfolioSeeder
```

### 8. Compilar Assets
```bash
# Desarrollo
npm run dev

# Producción
npm run build
```

### 9. Iniciar Servidor
```bash
php artisan serve
```

Abre http://localhost:8000

---

## ⚙️ Configuración

### PagoFácil (Pagos QR)

Configura en `.env`:
```env
PAGOFACIL_TOKEN_SERVICE=tu_token_servicio
PAGOFACIL_TOKEN_SECRET=tu_token_secreto
PAGOFACIL_URL=https://serviciostigomoney.pagofacil.com.bo/api
```

### Usuarios por Defecto

Después de ejecutar seeders:
- **Email**: propietaria@confecciones.com
- **Password**: password

---

## 🚀 Uso

### Roles y Permisos

| Rol | Acceso |
|-----|--------|
| **PROPIETARIO** | Acceso total al sistema |
| **AYUDANTE** | Pedidos, inventario, pagos (sin usuarios/reportes) |
| **CLIENTE** | Mi Cuenta, Mis Pedidos |

### Flujo de Trabajo

1. **Cliente Anónimo**: Visita catálogo → Solicita pedido (sin login)
2. **Cliente Registrado**: Login → Ve dashboard → Gestiona sus pedidos
3. **Administrador**: Login → Gestiona pedidos → Asigna presupuestos → Genera QR pago → Consulta reportes

---

## 📂 Estructura del Proyecto

```
confecciones-soledad-laravel/
├── app/
│   ├── Http/Controllers/
│   │   ├── PublicController.php      # Vistas públicas
│   │   ├── ClienteController.php     # Panel cliente
│   │   ├── UserController.php
│   │   ├── OrderController.php
│   │   ├── PaymentController.php     # PagoFácil QR
│   │   └── ...
│   ├── Models/
│   │   ├── User.php
│   │   ├── Order.php
│   │   ├── VisitaPagina.php         # Contador visitas
│   │   └── ...
│   └── Services/
│       └── PagoFacilService.php
├── database/
│   ├── migrations/
│   ├── factories/
│   └── seeders/
├── resources/
│   ├── js/
│   │   ├── Pages/
│   │   │   ├── Public/              # Home, Catálogo, etc.
│   │   │   ├── Cliente/             # MiCuenta, MisPedidos
│   │   │   └── ...                  # Admin panels
│   │   └── Components/
│   │       ├── Logo.vue
│   │       ├── ThemeSelector.vue
│   │       ├── AccessibilityPanel.vue
│   │       └── VisitCounter.vue
│   ├── css/
│   │   ├── app.css
│   │   └── themes.css               # Sistema de temas
│   └── views/
│       └── app.blade.php
├── routes/
│   └── web.php
├── tests/
│   └── Feature/
├── docs/                            # Documentación adicional
└── README.md
```

---

## 🧪 Testing

### Ejecutar Todos los Tests
```bash
php artisan test
```

### Tests Específicos
```bash
php artisan test tests/Feature/PublicViewsTest.php
php artisan test tests/Feature/ClienteTest.php
```

### Cobertura
- **45+ tests** implementados
- **Cobertura**: ~75%

---

## 🌐 Despliegue

### Servidor de Producción

1. **Clonar y configurar** (pasos 1-7 de instalación)

2. **Optimizar Autoload**
```bash
composer install --optimize-autoloader --no-dev
```

3. **Compilar Assets**
```bash
npm run build
```

4. **Optimizar Laravel**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

5. **Permisos**
```bash
chmod -R 755 storage bootstrap/cache
```

6. **Configurar Nginx/Apache**

Ejemplo Nginx:
```nginx
server {
    listen 80;
    server_name confecciones-soledad.com;
    root /var/www/confecciones-soledad/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
    }
}
```

---

## 📚 Documentación Adicional

- **[API Endpoints](docs/api-endpoints.md)** - Documentación completa de rutas
- **[PagoFácil Integration](docs/pagofacil-integration.md)** - Guía integración QR
- **[Arquitectura](docs/README.md)** - Decisiones técnicas

---

## 🐛 Soporte

**Desarrollado para**: Confecciones Soledad
**Contacto**: contacto@confeccionessoledad.com
**Tel**: +591 75123456

---

## � Licencia

Este proyecto es propiedad de **Confecciones Soledad**. Todos los derechos reservados.

---

**🎉 Sistema Listo para Producción**

_Desarrollado con Laravel 11, Vue 3, Inertia.js y PostgreSQL._
