# Documentación API de Citas Médicas

Esta API proporciona endpoints para la gestión de citas médicas, incluyendo programación, procesamiento de pagos y autenticación. Construida con Laravel 11, implementa un sistema completo de citas médicas con control de acceso basado en roles.

## Tabla de Contenidos

-   [Requisitos](#requisitos)
-   [Instalación](#instalación)
-   [Autenticación](#autenticación)
-   [Endpoints de la API](#endpoints-de-la-api)
-   [Estructura del Proyecto](#estructura-del-proyecto)
-   [Manejo de Errores](#manejo-de-errores)

## Requisitos

-   Docker

## Instalación

1. Clonar el repositorio:

```bash
git clone https://github.com/nicortezm/citas-medicas
```

2. Copiar .env.example a .env y configurar la base de datos (se sugiere dejar igual, es un ambiente de desarrollo):

```bash
cp .env.example .env
```

3. Iniciar proyecto:

```bash
docker compose up -d
```

5. Ejecutar migraciones:

```bash
 docker-compose exec app php artisan migrate --seed
```

## Autenticación

La API utiliza Laravel Sanctum para la autenticación. Todas las rutas autenticadas requieren un token Bearer en el encabezado Authorization.

### Endpoints de Autenticación

#### Registrar Nuevo Paciente

```
POST /api/auth/register
```

Parámetros del cuerpo:

-   `name` (requerido): string
-   `email` (requerido): string, email válido
-   `password` (requerido): string, mínimo 8 caracteres
-   `password_confirmation` (requerido): string

#### Iniciar Sesión

```
POST /api/auth/login
```

Parámetros del cuerpo:

-   `email` (requerido): string
-   `password` (requerido): string

#### Cerrar Sesión

```
POST /api/auth/logout
```

Requiere token de autenticación

## Endpoints de la API

### Citas

#### Crear Cita

```
POST /api/appointments
```

Encabezados requeridos:

-   Authorization: Bearer {token}

Parámetros del cuerpo:

-   `datetime` (requerido): fecha (Y-m-d H:i)
-   `doctor_email` (requerido): string

#### Ver Citas del Día (Solo Médicos)

```
GET /api/appointments/view-appointments
```

Encabezados requeridos:

-   Authorization: Bearer {token}

#### Confirmar Cita (Solo Médicos)

```
POST /api/appointments/confirm-appointment
```

Encabezados requeridos:

-   Authorization: Bearer {token}

Parámetros del cuerpo:

-   `appointment_id` (requerido): entero

### Pagos

#### Obtener Enlace de Pago

```
GET /api/payments/link/{appointment_id}
```

Encabezados requeridos:

-   Authorization: Bearer {token}

#### Callbacks de Pago

```
GET /api/payments/callback-success
GET /api/payments/callback-failure
```

### Gestión de Médicos

#### Crear Médico (Solo Admin)

```
POST /api/create-doctor
```

Encabezados requeridos:

-   Authorization: Bearer {token}

Parámetros del cuerpo:

-   `name` (requerido): string
-   `email` (requerido): string
-   `password` (requerido): string

## Estructura del Proyecto

```
app/
├── Casts/            # Clases de conversión personalizadas
├── Enums/            # Clases de enumeración
├── Exceptions/       # Manejadores de excepciones personalizados
├── Helpers/          # Funciones auxiliares
├── Http/
│   ├── Controllers/  # Controladores de la API
│   └── Requests/     # Requests para validación
├── Models/           # Modelos Eloquent
├── Observers/        # Observadores de modelos
├── Policies/         # Políticas de autorización
├── Providers/        # Proveedores de servicios
└── Services/         # Servicios de lógica de negocio
```

## Manejo de Errores

La API devuelve códigos de estado HTTP estándar:

-   200: Éxito
-   201: Creado
-   400: Solicitud Incorrecta
-   401: No Autorizado
-   403: Prohibido
-   404: No Encontrado
-   500: Error del Servidor

Las respuestas de error siguen este formato:

```json
{
    "message": "Mensaje de error aquí",
    "errors": {
        "campo": ["Descripción del error"]
    }
}
```

## Reglas de Validación

-   Las citas solo pueden programarse entre las 7:00-12:00 y 14:00-18:00
-   No se pueden programar citas en horarios ya ocupados
-   Las citas deben ser pagadas antes de la confirmación
-   Los médicos solo pueden ver y gestionar sus propias citas
-   Los pacientes solo pueden ver y gestionar sus propias citas

## Medidas de Seguridad

-   Autenticación de API usando Laravel Sanctum
-   Middleware CORS habilitado
-   Validación de entrada usando Form Requests
-   Control de acceso basado en roles
-   Prevención de inyección SQL a través del query builder de Laravel
-   Protección XSS a través de las características de seguridad incorporadas de Laravel
