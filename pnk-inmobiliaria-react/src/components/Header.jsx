import React from 'react';
import { AppBar, Toolbar, Typography, Button, Box } from '@mui/material';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';

const Header = () => {
  const { user, logout } = useAuth();
  const navigate = useNavigate();

  const handleLogout = () => {
    logout();
    navigate('/');
  };

  return (
    <AppBar position="static" sx={{ backgroundColor: '#f9f0ff', color: '#9c27b0', boxShadow: '0 4px 6px rgba(0, 0, 0, 0.05)' }}>
      <Toolbar>
        <Box sx={{ display: 'flex', alignItems: 'center', flexGrow: 1 }}>
          <img 
            src="/img/Logo.png" 
            alt="Logo PNK" 
            style={{ width: '50px', height: '50px', marginRight: '15px' }}
          />
          <Typography 
            variant="h6" 
            component={Link} 
            to="/" 
            sx={{ 
              textDecoration: 'none', 
              color: '#9c27b0', 
              fontWeight: 'bold',
              fontSize: '1.5rem'
            }}
          >
            PNK INMOBILIARIA
          </Typography>
        </Box>

        <Box sx={{ display: 'flex', gap: 2, alignItems: 'center' }}>
          {user ? (
            <>
              <Typography sx={{ color: '#000', marginRight: 2 }}>
                Bienvenido, {user.nombre || user.usuario}
              </Typography>
              {user.tipo === 'propietario' && (
                <Button 
                  component={Link} 
                  to="/dashboard-propietario"
                  sx={{ 
                    backgroundColor: '#7e57c2', 
                    color: 'white',
                    '&:hover': { backgroundColor: '#673ab7' }
                  }}
                >
                  Mi Panel
                </Button>
              )}
              {user.tipo === 'admin' && (
                <Button 
                  component={Link} 
                  to="/dashboard"
                  sx={{ 
                    backgroundColor: '#7e57c2', 
                    color: 'white',
                    '&:hover': { backgroundColor: '#673ab7' }
                  }}
                >
                  Dashboard
                </Button>
              )}
              <Button 
                onClick={handleLogout}
                sx={{ 
                  backgroundColor: '#7e57c2', 
                  color: 'white',
                  '&:hover': { backgroundColor: '#673ab7' }
                }}
              >
                Cerrar Sesión
              </Button>
            </>
          ) : (
            <>
              <Button 
                component={Link} 
                to="/register-propietario"
                sx={{ 
                  backgroundColor: '#7e57c2', 
                  color: 'white',
                  '&:hover': { backgroundColor: '#673ab7' }
                }}
              >
                Crear Propietario
              </Button>
              <Button 
                component={Link} 
                to="/register-gestor"
                sx={{ 
                  backgroundColor: '#7e57c2', 
                  color: 'white',
                  '&:hover': { backgroundColor: '#673ab7' }
                }}
              >
                Crear Gestor
              </Button>
              <Button 
                component={Link} 
                to="/login"
                sx={{ 
                  backgroundColor: '#7e57c2', 
                  color: 'white',
                  '&:hover': { backgroundColor: '#673ab7' }
                }}
              >
                Iniciar Sesión
              </Button>
            </>
          )}
        </Box>
      </Toolbar>
    </AppBar>
  );
};

export default Header;