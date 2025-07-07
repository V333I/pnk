import React, { useState } from 'react';
import { 
  Paper, 
  TextField, 
  Button, 
  Typography, 
  Box,
  Alert
} from '@mui/material';
import { useAuth } from '../contexts/AuthContext';
import { useNavigate, Link } from 'react-router-dom';

const LoginForm = () => {
  const [credentials, setCredentials] = useState({
    usuario: '',
    password: ''
  });
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);
  const { login } = useAuth();
  const navigate = useNavigate();

  const handleChange = (e) => {
    setCredentials({
      ...credentials,
      [e.target.name]: e.target.value
    });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setError('');

    // Validaciones básicas
    if (!credentials.usuario || !credentials.password) {
      setError('Todos los campos son obligatorios');
      setLoading(false);
      return;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(credentials.usuario)) {
      setError('Debe ingresar un email válido');
      setLoading(false);
      return;
    }

    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
    if (!passwordRegex.test(credentials.password)) {
      setError('La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial');
      setLoading(false);
      return;
    }

    try {
      const result = await login(credentials);
      if (result.success) {
        if (result.user.tipo === 'admin') {
          navigate('/dashboard');
        } else if (result.user.tipo === 'propietario') {
          navigate('/dashboard-propietario');
        } else {
          navigate('/');
        }
      } else {
        setError(result.message);
      }
    } catch (error) {
      setError('Error al iniciar sesión');
    } finally {
      setLoading(false);
    }
  };

  return (
    <Box sx={{ display: 'flex', justifyContent: 'center', mb: 4 }}>
      <Paper 
        elevation={3}
        sx={{ 
          p: 4, 
          maxWidth: 400, 
          width: '100%',
          borderRadius: 3
        }}
      >
        <Typography variant="h5" align="center" gutterBottom sx={{ color: '#9c27b0', fontWeight: 'bold' }}>
          Autenticación
        </Typography>
        
        <Box sx={{ textAlign: 'center', mb: 3 }}>
          <img 
            src="/img/key.png" 
            alt="Login" 
            style={{ width: '60px', height: '60px' }}
          />
        </Box>

        {error && (
          <Alert severity="error" sx={{ mb: 2 }}>
            {error}
          </Alert>
        )}

        <form onSubmit={handleSubmit}>
          <TextField
            fullWidth
            label="Usuario (Email)"
            name="usuario"
            type="email"
            value={credentials.usuario}
            onChange={handleChange}
            margin="normal"
            required
            placeholder="ingrese email"
          />
          
          <TextField
            fullWidth
            label="Contraseña"
            name="password"
            type="password"
            value={credentials.password}
            onChange={handleChange}
            margin="normal"
            required
            placeholder="ingrese contraseña"
          />

          <Button
            type="submit"
            fullWidth
            variant="contained"
            disabled={loading}
            sx={{ 
              mt: 3, 
              mb: 2,
              backgroundColor: '#9c27b0',
              '&:hover': { backgroundColor: '#7b1fa2' }
            }}
          >
            {loading ? 'Ingresando...' : 'Ingresar'}
          </Button>
        </form>

        <Box sx={{ textAlign: 'center' }}>
          <Link 
            to="/recover-password" 
            style={{ 
              color: '#9c27b0', 
              textDecoration: 'none',
              fontWeight: 'bold'
            }}
          >
            Recuperar Contraseña
          </Link>
        </Box>
      </Paper>
    </Box>
  );
};

export default LoginForm;