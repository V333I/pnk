import React, { useState } from 'react';
import { 
  Container, 
  Paper, 
  TextField, 
  Button, 
  Typography, 
  Box,
  Alert
} from '@mui/material';
import { Link } from 'react-router-dom';
import { authAPI } from '../services/api';
import Swal from 'sweetalert2';

const RecoverPassword = () => {
  const [email, setEmail] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setError('');

    if (!email) {
      setError('Por favor, ingrese su correo electrónico');
      setLoading(false);
      return;
    }

    try {
      // Mostrar el contador de 2 minutos
      const result = await Swal.fire({
        title: 'Procesando solicitud',
        html: `
          <div style="text-align: center;">
            <p>Tiene 2 minutos para validar correo electrónico</p>
            <h2 id="contador" style="color: #9c27b0; font-size: 2rem;">2:00</h2>
          </div>
        `,
        showCancelButton: true,
        showConfirmButton: true,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        allowOutsideClick: false,
        allowEscapeKey: false,
        timer: 120000, // 2 minutos
        timerProgressBar: true,
        didOpen: () => {
          let tiempo = 120;
          const contador = document.getElementById('contador');
          
          const intervalo = setInterval(() => {
            tiempo--;
            const minutos = Math.floor(tiempo / 60);
            const segundos = tiempo % 60;
            if (contador) {
              contador.textContent = `${minutos}:${segundos.toString().padStart(2, '0')}`;
            }
            
            if (tiempo <= 0) {
              clearInterval(intervalo);
            }
          }, 1000);
        }
      });

      if (result.isConfirmed) {
        // Usuario aceptó
        await Swal.fire({
          icon: 'success',
          title: '¡Éxito!',
          text: 'Se ha enviado un correo con instrucciones para recuperar tu contraseña',
          confirmButtonColor: '#9c27b0'
        });
      } else if (result.dismiss === Swal.DismissReason.cancel) {
        // Usuario canceló
        await Swal.fire({
          icon: 'info',
          title: 'Cancelado',
          text: 'La operación ha sido cancelada',
          confirmButtonColor: '#9c27b0'
        });
      } else {
        // Tiempo expirado
        await Swal.fire({
          icon: 'info',
          title: 'Tiempo expirado',
          text: 'El tiempo para procesar la solicitud ha expirado',
          confirmButtonColor: '#9c27b0'
        });
      }
    } catch (error) {
      setError('Error al procesar la solicitud');
    } finally {
      setLoading(false);
    }
  };

  return (
    <Container maxWidth="sm" sx={{ py: 4 }}>
      <Paper elevation={3} sx={{ p: 4, borderRadius: 3 }}>
        <Typography variant="h4" align="center" gutterBottom sx={{ color: '#9c27b0', fontWeight: 'bold' }}>
          Recuperar Contraseña
        </Typography>

        {error && (
          <Alert severity="error" sx={{ mb: 2 }}>
            {error}
          </Alert>
        )}

        <form onSubmit={handleSubmit}>
          <TextField
            fullWidth
            label="Correo Electrónico"
            type="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            margin="normal"
            required
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
            {loading ? 'Procesando...' : 'Recuperar'}
          </Button>
        </form>

        <Box sx={{ textAlign: 'center' }}>
          <Button 
            component={Link}
            to="/"
            sx={{ color: '#9c27b0' }}
          >
            Volver
          </Button>
        </Box>
      </Paper>
    </Container>
  );
};

export default RecoverPassword;