import React, { useState, useEffect } from 'react';
import { 
  Container, 
  Typography, 
  Grid, 
  Card, 
  CardMedia, 
  CardContent, 
  Button, 
  Box,
  FormControl,
  InputLabel,
  Select,
  MenuItem,
  Paper
} from '@mui/material';
import { Link } from 'react-router-dom';
import { propertiesAPI, locationsAPI } from '../services/api';
import { useAuth } from '../contexts/AuthContext';
import LoginForm from '../components/LoginForm';

const Home = () => {
  const { user } = useAuth();
  const [properties, setProperties] = useState([]);
  const [filters, setFilters] = useState({
    tipo_propiedad: '',
    region: '',
    provincia: '',
    comuna: '',
    sector: ''
  });
  const [locations, setLocations] = useState({
    regions: [],
    provinces: [],
    communes: [],
    sectors: []
  });
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadProperties();
    loadRegions();
  }, []);

  useEffect(() => {
    if (filters.region) {
      loadProvinces(filters.region);
    }
  }, [filters.region]);

  useEffect(() => {
    if (filters.provincia) {
      loadCommunes(filters.provincia);
    }
  }, [filters.provincia]);

  useEffect(() => {
    if (filters.comuna) {
      loadSectors(filters.comuna);
    }
  }, [filters.comuna]);

  const loadProperties = async () => {
    try {
      const response = await propertiesAPI.getAll(filters);
      setProperties(response.data);
    } catch (error) {
      console.error('Error loading properties:', error);
    } finally {
      setLoading(false);
    }
  };

  const loadRegions = async () => {
    try {
      const response = await locationsAPI.getRegions();
      setLocations(prev => ({ ...prev, regions: response.data }));
    } catch (error) {
      console.error('Error loading regions:', error);
    }
  };

  const loadProvinces = async (regionId) => {
    try {
      const response = await locationsAPI.getProvinces(regionId);
      setLocations(prev => ({ 
        ...prev, 
        provinces: response.data,
        communes: [],
        sectors: []
      }));
      setFilters(prev => ({ ...prev, provincia: '', comuna: '', sector: '' }));
    } catch (error) {
      console.error('Error loading provinces:', error);
    }
  };

  const loadCommunes = async (provinceId) => {
    try {
      const response = await locationsAPI.getCommunes(provinceId);
      setLocations(prev => ({ 
        ...prev, 
        communes: response.data,
        sectors: []
      }));
      setFilters(prev => ({ ...prev, comuna: '', sector: '' }));
    } catch (error) {
      console.error('Error loading communes:', error);
    }
  };

  const loadSectors = async (communeId) => {
    try {
      const response = await locationsAPI.getSectors(communeId);
      setLocations(prev => ({ ...prev, sectors: response.data }));
      setFilters(prev => ({ ...prev, sector: '' }));
    } catch (error) {
      console.error('Error loading sectors:', error);
    }
  };

  const handleFilterChange = (field, value) => {
    setFilters(prev => ({ ...prev, [field]: value }));
  };

  const handleSearch = () => {
    loadProperties();
  };

  const formatPrice = (price) => {
    return new Intl.NumberFormat('es-CL', {
      style: 'currency',
      currency: 'CLP'
    }).format(price);
  };

  return (
    <Container maxWidth="xl" sx={{ py: 4 }}>
      {!user && <LoginForm />}
      
      {/* Buscador de Propiedades */}
      <Paper 
        elevation={3}
        sx={{ 
          background: 'linear-gradient(135deg, #7e57c2 0%, #5e35b1 100%)',
          color: 'white',
          p: 4,
          mb: 4,
          borderRadius: 3
        }}
      >
        <Typography variant="h4" align="center" gutterBottom sx={{ fontWeight: 600, mb: 3 }}>
          BUSCADOR DE PROPIEDADES
        </Typography>
        
        <Grid container spacing={3}>
          <Grid item xs={12} md={6} lg={2.4}>
            <FormControl fullWidth>
              <InputLabel sx={{ color: 'white' }}>Tipo</InputLabel>
              <Select
                value={filters.tipo_propiedad}
                onChange={(e) => handleFilterChange('tipo_propiedad', e.target.value)}
                sx={{ 
                  backgroundColor: 'rgba(255,255,255,0.1)',
                  color: 'white',
                  '& .MuiOutlinedInput-notchedOutline': { borderColor: 'rgba(255,255,255,0.3)' }
                }}
              >
                <MenuItem value="">Seleccione tipo</MenuItem>
                <MenuItem value="1">Casas</MenuItem>
                <MenuItem value="2">Departamentos</MenuItem>
                <MenuItem value="3">Terrenos</MenuItem>
              </Select>
            </FormControl>
          </Grid>

          <Grid item xs={12} md={6} lg={2.4}>
            <FormControl fullWidth>
              <InputLabel sx={{ color: 'white' }}>Región</InputLabel>
              <Select
                value={filters.region}
                onChange={(e) => handleFilterChange('region', e.target.value)}
                sx={{ 
                  backgroundColor: 'rgba(255,255,255,0.1)',
                  color: 'white',
                  '& .MuiOutlinedInput-notchedOutline': { borderColor: 'rgba(255,255,255,0.3)' }
                }}
              >
                <MenuItem value="">Seleccione región</MenuItem>
                {locations.regions.map((region) => (
                  <MenuItem key={region.idregion} value={region.idregion}>
                    {region.nombre_region}
                  </MenuItem>
                ))}
              </Select>
            </FormControl>
          </Grid>

          <Grid item xs={12} md={6} lg={2.4}>
            <FormControl fullWidth>
              <InputLabel sx={{ color: 'white' }}>Provincia</InputLabel>
              <Select
                value={filters.provincia}
                onChange={(e) => handleFilterChange('provincia', e.target.value)}
                disabled={!filters.region}
                sx={{ 
                  backgroundColor: 'rgba(255,255,255,0.1)',
                  color: 'white',
                  '& .MuiOutlinedInput-notchedOutline': { borderColor: 'rgba(255,255,255,0.3)' }
                }}
              >
                <MenuItem value="">Seleccione provincia</MenuItem>
                {locations.provinces.map((province) => (
                  <MenuItem key={province.idprovincias} value={province.idprovincias}>
                    {province.nombre_provincia}
                  </MenuItem>
                ))}
              </Select>
            </FormControl>
          </Grid>

          <Grid item xs={12} md={6} lg={2.4}>
            <FormControl fullWidth>
              <InputLabel sx={{ color: 'white' }}>Comuna</InputLabel>
              <Select
                value={filters.comuna}
                onChange={(e) => handleFilterChange('comuna', e.target.value)}
                disabled={!filters.provincia}
                sx={{ 
                  backgroundColor: 'rgba(255,255,255,0.1)',
                  color: 'white',
                  '& .MuiOutlinedInput-notchedOutline': { borderColor: 'rgba(255,255,255,0.3)' }
                }}
              >
                <MenuItem value="">Seleccione comuna</MenuItem>
                {locations.communes.map((commune) => (
                  <MenuItem key={commune.idcomunas} value={commune.idcomunas}>
                    {commune.nombre_comuna}
                  </MenuItem>
                ))}
              </Select>
            </FormControl>
          </Grid>

          <Grid item xs={12} md={6} lg={2.4}>
            <FormControl fullWidth>
              <InputLabel sx={{ color: 'white' }}>Sector</InputLabel>
              <Select
                value={filters.sector}
                onChange={(e) => handleFilterChange('sector', e.target.value)}
                disabled={!filters.comuna}
                sx={{ 
                  backgroundColor: 'rgba(255,255,255,0.1)',
                  color: 'white',
                  '& .MuiOutlinedInput-notchedOutline': { borderColor: 'rgba(255,255,255,0.3)' }
                }}
              >
                <MenuItem value="">Seleccione sector</MenuItem>
                {locations.sectors.map((sector) => (
                  <MenuItem key={sector.idsectores} value={sector.idsectores}>
                    {sector.nombre_sector}
                  </MenuItem>
                ))}
              </Select>
            </FormControl>
          </Grid>
        </Grid>

        <Box sx={{ textAlign: 'center', mt: 3 }}>
          <Button 
            variant="contained" 
            onClick={handleSearch}
            sx={{ 
              backgroundColor: 'white',
              color: '#7e57c2',
              fontWeight: 600,
              px: 4,
              py: 1,
              '&:hover': { backgroundColor: '#f8f9fa' }
            }}
          >
            Buscar Propiedades
          </Button>
        </Box>
      </Paper>

      {/* Galería de Propiedades */}
      <Grid container spacing={3}>
        {properties.map((property) => (
          <Grid item xs={12} sm={6} md={4} key={property.num_propiedad}>
            <Card 
              sx={{ 
                height: '100%',
                transition: 'transform 0.2s',
                '&:hover': { transform: 'scale(1.02)' }
              }}
            >
              <CardMedia
                component="img"
                height="200"
                image={property.foto || '/img/casa.jpg'}
                alt={property.titulopropiedad}
              />
              <CardContent>
                <Typography variant="h6" component="h3" gutterBottom>
                  {property.titulopropiedad}
                </Typography>
                <Box sx={{ mt: 2 }}>
                  <Typography 
                    variant="h6" 
                    sx={{ color: '#9c27b0', fontWeight: 'bold' }}
                  >
                    UF {property.precio_uf?.toLocaleString('es-CL')}
                  </Typography>
                  <Typography 
                    variant="body1" 
                    sx={{ color: '#7e57c2', fontWeight: 'bold' }}
                  >
                    {formatPrice(property.precio_pesos)}
                  </Typography>
                  <Button 
                    component={Link}
                    to={`/property/${property.num_propiedad}`}
                    variant="contained"
                    sx={{ 
                      mt: 2,
                      backgroundColor: '#007bff',
                      '&:hover': { backgroundColor: '#0056b3' }
                    }}
                  >
                    Ver más
                  </Button>
                </Box>
              </CardContent>
            </Card>
          </Grid>
        ))}
      </Grid>

      {properties.length === 0 && !loading && (
        <Box sx={{ textAlign: 'center', py: 4 }}>
          <Typography variant="h6" color="text.secondary">
            No se encontraron propiedades con los filtros seleccionados.
          </Typography>
        </Box>
      )}
    </Container>
  );
};

export default Home;