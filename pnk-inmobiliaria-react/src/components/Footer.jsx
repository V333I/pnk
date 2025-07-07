import React from 'react';
import { Box, Typography, Link, Grid } from '@mui/material';

const Footer = () => {
  return (
    <Box 
      component="footer" 
      sx={{ 
        backgroundColor: '#343a40', 
        color: 'white', 
        padding: '2rem 0',
        marginTop: 'auto'
      }}
    >
      <Grid container spacing={3} sx={{ maxWidth: '1200px', margin: '0 auto', padding: '0 20px' }}>
        <Grid item xs={12} md={4}>
          <Box sx={{ display: 'flex', flexDirection: 'column', alignItems: 'center' }}>
            <img 
              src="/img/Logo.png" 
              alt="Logo PNK" 
              style={{ width: '50px', height: '50px', marginBottom: '10px' }}
            />
            <Typography variant="h6" sx={{ fontWeight: 'bold', fontSize: '14px' }}>
              PNK INMOBILIARIA
            </Typography>
          </Box>
        </Grid>

        <Grid item xs={12} md={4}>
          <Box sx={{ display: 'flex', justifyContent: 'center', gap: 2 }}>
            <Link 
              href="/register-propietario" 
              sx={{ 
                color: 'white', 
                textDecoration: 'none',
                '&:hover': { textDecoration: 'underline' }
              }}
            >
              Registro Propietario
            </Link>
            <Link 
              href="/register-gestor" 
              sx={{ 
                color: 'white', 
                textDecoration: 'none',
                '&:hover': { textDecoration: 'underline' }
              }}
            >
              Registro Gestor
            </Link>
          </Box>
        </Grid>

        <Grid item xs={12} md={4}>
          <Box sx={{ display: 'flex', justifyContent: 'center', gap: 2 }}>
            <Link 
              href="https://www.instagram.com/" 
              target="_blank"
              rel="noopener noreferrer"
            >
              <img 
                src="/img/logo-insta.png" 
                alt="Instagram" 
                style={{ width: '30px', height: '30px' }}
              />
            </Link>
            <Link 
              href="https://www.linkedin.com/" 
              target="_blank"
              rel="noopener noreferrer"
            >
              <img 
                src="/img/linkedin.png" 
                alt="LinkedIn" 
                style={{ width: '30px', height: '30px' }}
              />
            </Link>
          </Box>
        </Grid>
      </Grid>

      <Box sx={{ textAlign: 'center', marginTop: '20px', borderTop: '1px solid #666', paddingTop: '10px' }}>
        <Typography variant="body2" sx={{ fontSize: '12px', color: '#666' }}>
          © 2025 Todos los derechos Reservados PNK Inmobiliaria
        </Typography>
      </Box>
    </Box>
  );
};

export default Footer;