import React, { useState, useEffect } from 'react';
import { 
  Container, 
  Typography, 
  Grid, 
  Card, 
  CardMedia, 
  Box,
  Chip,
  Button,
  Paper
} from '@mui/material';
import { 
  Home, 
  Bed, 
  Bathtub, 
  Straighten, 
  LocationOn,
  CheckCircle,
  Cancel
} from '@mui/icons-material';
import { useParams, useNavigate } from 'react-router-dom';
import { propertiesAPI } from '../services/api';

const PropertyDetail = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const [property, setProperty] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadProperty();
  }, [id]);

  const loadProperty = async () => {
    try {
      const response = await propertiesAPI.getById(id);
      setProperty(response.data);
    } catch (error) {
      console.error('Error loading property:', error);
      navigate('/');
    } finally {
      setLoading(false);
    }
  };

  const formatPrice = (price) => {
    return new Intl.NumberFormat('es-CL', {
      style: 'currency',
      currency: 'CLP'
    }).format(price);
  };

  if (loading) {
    return (
      <Container maxWidth="lg" sx={{ py: 4 }}>
        <Typography>Cargando...</Typography>
      </Container>
    );
  }

  if (!property) {
    return (
      <Container maxWidth="lg" sx={{ py: 4 }}>
        <Typography>Propiedad no encontrada</Typography>
      </Container>
    );
  }

  return (
    <Container maxWidth="lg" sx={{ py: 4 }}>
      <Typography variant="h3" gutterBottom sx={{ color: '#9c27b0', fontWeight: 'bold' }}>
        {property.titulopropiedad}
      </Typography>

      <Grid container spacing={4}>
        {/* Galería de imágenes */}
        <Grid item xs={12} md={8}>
          <Card>
            <CardMedia
              component="img"
              height="400"
              image={property.foto || '/img/casa.jpg'}
              alt={property.titulopropiedad}
            />
          </Card>
        </Grid>

        {/* Información principal */}
        <Grid item xs={12} md={4}>
          <Paper elevation={3} sx={{ p: 3, background: 'linear-gradient(135deg, #28a745, #20c997)', color: 'white' }}>
            <Typography variant="h4" gutterBottom sx={{ fontWeight: 'bold' }}>
              UF {property.precio_uf?.toLocaleString('es-CL')}
            </Typography>
            <Typography variant="h6" sx={{ opacity: 0.9 }}>
              {formatPrice(property.precio_pesos)}
            </Typography>
          </Paper>

          {/* Características principales */}
          <Grid container spacing={2} sx={{ mt: 2 }}>
            <Grid item xs={6}>
              <Paper sx={{ p: 2, textAlign: 'center' }}>
                <Home sx={{ fontSize: 30, color: '#007bff', mb: 1 }} />
                <Typography variant="body2">Tipo</Typography>
                <Typography variant="h6">{property.tipo}</Typography>
              </Paper>
            </Grid>
            <Grid item xs={6}>
              <Paper sx={{ p: 2, textAlign: 'center' }}>
                <Bed sx={{ fontSize: 30, color: '#007bff', mb: 1 }} />
                <Typography variant="body2">Dormitorios</Typography>
                <Typography variant="h6">{property.cant_domitorios}</Typography>
              </Paper>
            </Grid>
            <Grid item xs={6}>
              <Paper sx={{ p: 2, textAlign: 'center' }}>
                <Bathtub sx={{ fontSize: 30, color: '#007bff', mb: 1 }} />
                <Typography variant="body2">Baños</Typography>
                <Typography variant="h6">{property.cant_banos}</Typography>
              </Paper>
            </Grid>
            <Grid item xs={6}>
              <Paper sx={{ p: 2, textAlign: 'center' }}>
                <Straighten sx={{ fontSize: 30, color: '#007bff', mb: 1 }} />
                <Typography variant="body2">Área Total</Typography>
                <Typography variant="h6">{property.area_total} m²</Typography>
              </Paper>
            </Grid>
          </Grid>
        </Grid>

        {/* Ubicación */}
        <Grid item xs={12}>
          <Paper sx={{ p: 3 }}>
            <Typography variant="h5" gutterBottom sx={{ color: '#007bff', display: 'flex', alignItems: 'center', gap: 1 }}>
              <LocationOn /> Ubicación
            </Typography>
            <Typography variant="body1">
              {property.nombre_sector}, {property.nombre_comuna}, {property.nombre_provincia}, {property.nombre_region}
            </Typography>
          </Paper>
        </Grid>

        {/* Amenidades */}
        <Grid item xs={12} md={6}>
          <Paper sx={{ p: 3 }}>
            <Typography variant="h5" gutterBottom sx={{ color: '#007bff' }}>
              Amenidades
            </Typography>
            <Grid container spacing={1}>
              {[
                { key: 'bodega', label: 'Bodega' },
                { key: 'estacionamiento', label: 'Estacionamiento' },
                { key: 'logia', label: 'Logia' },
                { key: 'cocinaamoblada', label: 'Cocina Amoblada' },
                { key: 'antejardin', label: 'Antejardín' },
                { key: 'patiotrasero', label: 'Patio Trasero' },
                { key: 'piscina', label: 'Piscina' }
              ].map((amenity) => (
                <Grid item xs={6} key={amenity.key}>
                  <Box sx={{ display: 'flex', alignItems: 'center', gap: 1, p: 1 }}>
                    {property[amenity.key] ? (
                      <CheckCircle sx={{ color: '#28a745' }} />
                    ) : (
                      <Cancel sx={{ color: '#dc3545' }} />
                    )}
                    <Typography 
                      variant="body2" 
                      sx={{ opacity: property[amenity.key] ? 1 : 0.5 }}
                    >
                      {amenity.label}
                    </Typography>
                  </Box>
                </Grid>
              ))}
            </Grid>
          </Paper>
        </Grid>

        {/* Descripción */}
        <Grid item xs={12} md={6}>
          <Paper sx={{ p: 3 }}>
            <Typography variant="h5" gutterBottom sx={{ color: '#007bff' }}>
              Descripción
            </Typography>
            <Typography variant="body1" sx={{ whiteSpace: 'pre-line' }}>
              {property.descripcion}
            </Typography>
          </Paper>
        </Grid>

        {/* Botón volver */}
        <Grid item xs={12}>
          <Box sx={{ textAlign: 'center', mt: 4 }}>
            <Button 
              variant="contained" 
              size="large"
              onClick={() => navigate('/')}
              sx={{ 
                backgroundColor: '#007bff',
                '&:hover': { backgroundColor: '#0056b3' }
              }}
            >
              Volver al inicio
            </Button>
          </Box>
        </Grid>
      </Grid>
    </Container>
  );
};

export default PropertyDetail;