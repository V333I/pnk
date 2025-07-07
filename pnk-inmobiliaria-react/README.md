# PNK Inmobiliaria - React Frontend

Este es el frontend de la aplicación PNK Inmobiliaria migrado de PHP a React.

## Características

- **React 18** con Vite como bundler
- **Material-UI** para componentes de interfaz
- **React Router** para navegación
- **Axios** para peticiones HTTP
- **SweetAlert2** para alertas
- **Autenticación** con JWT y cookies
- **Responsive Design** adaptable a todos los dispositivos

## Estructura del Proyecto

```
src/
├── components/          # Componentes reutilizables
├── contexts/           # Contextos de React (Auth, etc.)
├── pages/              # Páginas principales
├── services/           # Servicios API
└── App.jsx            # Componente principal
```

## Instalación y Desarrollo

1. Instalar dependencias:
```bash
npm install
```

2. Configurar variables de entorno:
```bash
cp .env.example .env
```

3. Ejecutar en modo desarrollo:
```bash
npm run dev
```

4. Construir para producción:
```bash
npm run build
```

## Funcionalidades Implementadas

### Autenticación
- Login para usuarios, gestores y propietarios
- Registro de propietarios y gestores
- Recuperación de contraseña
- Manejo de sesiones con JWT

### Gestión de Propiedades
- Listado de propiedades con filtros
- Vista detallada de propiedades
- CRUD completo para propietarios
- Galería de imágenes

### Panel de Administración
- Gestión de usuarios
- Gestión de gestores
- Gestión de propietarios
- Gestión de propiedades

### Panel de Propietario
- Registro de nuevas propiedades
- Edición de propiedades existentes
- Gestión de imágenes
- Vista de estadísticas

## Próximos Pasos

1. **Backend API**: Crear el backend en Node.js/Express
2. **Base de Datos**: Migrar a AWS RDS
3. **Despliegue**: Configurar en AWS (S3 + CloudFront para frontend, EC2/ECS para backend)
4. **CI/CD**: Implementar pipeline de despliegue automático

## Tecnologías Utilizadas

- React 18
- Material-UI v5
- React Router v6
- Axios
- SweetAlert2
- Vite
- JavaScript ES6+