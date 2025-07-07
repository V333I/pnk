import React, { useState, useEffect } from 'react';
import { 
  Container, 
  Typography, 
  Grid, 
  Card, 
  CardContent, 
  Button,
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  Paper,
  IconButton,
  Dialog,
  DialogTitle,
  DialogContent,
  DialogActions,
  TextField,
  FormControl,
  InputLabel,
  Select,
  MenuItem,
  Box,
  Checkbox,
  FormControlLabel
} from '@mui/material';
import { Edit, Delete, PhotoCamera, Add } from '@mui/icons-material';
import { useAuth } from '../contexts/AuthContext';
import { propertiesAPI, locationsAPI } from '../services/api';
import { useNavigate } from 'react-router-dom';
import Swal from 'sweetalert2';

const DashboardPropietario = () => {
  const { user } = useAuth();
  const navigate = useNavigate();
  const [properties, setProperties] = useState([]);
  const [loading, setLoading] = useState(true);
  const [propertyDialog, setPropertyDialog] = useState({ open: false, property: null });
  const [locations, setLocations] = useState({
    regions: [],
    provinces: [],
    communes: [],
    sectors: []
  });

  useEffect(() => {
    if (!user || user.tipo !== 'propietario') {
      navigate('/');
      return;
    }
    loadProperties();
    loadRegions();
  }, [user, navigate]);

  const loadProperties = async () => {
    setLoading(true);
    try {
      const response = await propertiesAPI.getAll({ propietario: user.rut });
      setProperties(response.data);
    } catch (error) {
      console.error('Error loading properties:', error);
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Error al cargar las propiedades'
      });
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

  const handleNewProperty = () => {
    setPropertyDialog({ 
      open: true, 
      property: {
        titulo: '',
        tipo_propiedad: '',
        region: '',
        provincia: '',
        comuna: '',
        sector: '',
        precio_pesos: '',
        precio_uf: '',
        area_total: '',
        area_construida: '',
        cant_domitorios: '',
        cant_banos: '',
        descripcion: '',
        bodega: false,
        estacionamiento: false,
        logia: false,
        cocina_amoblada: false,
        antejardin: false,
        patio_trasero: false,
        piscina: false
      }
    });
  };

  const handleEditProperty = (property) => {
    setPropertyDialog({ open: true, property });
  };

  const handleDeleteProperty = async (id) => {
    const result = await Swal.fire({
      title: '¿Estás seguro?',
      text: 'Esta acción eliminará la propiedad y sus imágenes',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    });

    if (result.isConfirmed) {
      try {
        await propertiesAPI.delete(id);
        await Swal.fire({
          icon: 'success',
          title: '¡Eliminado!',
          text: 'La propiedad ha sido eliminada correctamente'
        });
        loadProperties();
      } catch (error) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Error al eliminar la propiedad'
        });
      }
    }
  };

  const handleSaveProperty = async (propertyData) => {
    try {
      if (propertyData.num_propiedad) {
        // Actualizar propiedad existente
        await propertiesAPI.update(propertyData.num_propiedad, propertyData);
      } else {
        // Crear nueva propiedad
        await propertiesAPI.create(propertyData);
      }
      
      setPropertyDialog({ open: false, property: null });
      await Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: 'Propiedad guardada correctamente'
      });
      
      loadProperties();
    } catch (error) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Error al guardar la propiedad'
      });
    }
  };

  return (
    <Container maxWidth="xl" sx={{ py: 4 }}>
      <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 4 }}>
        <Typography variant="h3" sx={{ color: '#9c27b0', fontWeight: 'bold' }}>
          Panel de Propietario
        </Typography>
        <Button 
          variant="contained" 
          startIcon={<Add />}
          onClick={handleNewProperty}
          sx={{ backgroundColor: '#7c3aed', '&:hover': { backgroundColor: '#6d28d9' } }}
        >
          Nueva Propiedad
        </Button>
      </Box>

      <Paper sx={{ p: 3 }}>
        <Typography variant="h5" gutterBottom>
          Mis Propiedades
        </Typography>
        
        {loading ? (
          <Typography>Cargando...</Typography>
        ) : (
          <TableContainer>
            <Table>
              <TableHead>
                <TableRow>
                  <TableCell>N°</TableCell>
                  <TableCell>Título</TableCell>
                  <TableCell>Tipo</TableCell>
                  <TableCell>Precio (CLP)</TableCell>
                  <TableCell>Precio (UF)</TableCell>
                  <TableCell>Estado</TableCell>
                  <TableCell>Acciones</TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {properties.map((property) => (
                  <TableRow key={property.num_propiedad}>
                    <TableCell>{property.num_propiedad}</TableCell>
                    <TableCell>{property.titulopropiedad}</TableCell>
                    <TableCell>{property.tipo}</TableCell>
                    <TableCell>{property.precio_pesos?.toLocaleString('es-CL')}</TableCell>
                    <TableCell>{property.precio_uf?.toLocaleString('es-CL')}</TableCell>
                    <TableCell>{property.estado === 1 ? 'Activo' : 'Inactivo'}</TableCell>
                    <TableCell>
                      <IconButton onClick={() => handleEditProperty(property)} color="primary">
                        <Edit />
                      </IconButton>
                      <IconButton color="info">
                        <PhotoCamera />
                      </IconButton>
                      <IconButton onClick={() => handleDeleteProperty(property.num_propiedad)} color="error">
                        <Delete />
                      </IconButton>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </TableContainer>
        )}
      </Paper>

      {/* Dialog para crear/editar propiedad */}
      <PropertyDialog 
        open={propertyDialog.open}
        property={propertyDialog.property}
        locations={locations}
        onClose={() => setPropertyDialog({ open: false, property: null })}
        onSave={handleSaveProperty}
      />
    </Container>
  );
};

// Componente para el diálogo de propiedad
const PropertyDialog = ({ open, property, locations, onClose, onSave }) => {
  const [formData, setFormData] = useState({});
  const [provinces, setProvinces] = useState([]);
  const [communes, setCommunes] = useState([]);
  const [sectors, setSectors] = useState([]);

  useEffect(() => {
    if (property) {
      setFormData({ ...property });
    }
  }, [property]);

  const handleChange = (field, value) => {
    setFormData(prev => ({ ...prev, [field]: value }));
  };

  const handleRegionChange = async (regionId) => {
    handleChange('region', regionId);
    try {
      const response = await locationsAPI.getProvinces(regionId);
      setProvinces(response.data);
      setCommunes([]);
      setSectors([]);
      handleChange('provincia', '');
      handleChange('comuna', '');
      handleChange('sector', '');
    } catch (error) {
      console.error('Error loading provinces:', error);
    }
  };

  const handleProvinceChange = async (provinceId) => {
    handleChange('provincia', provinceId);
    try {
      const response = await locationsAPI.getCommunes(provinceId);
      setCommunes(response.data);
      setSectors([]);
      handleChange('comuna', '');
      handleChange('sector', '');
    } catch (error) {
      console.error('Error loading communes:', error);
    }
  };

  const handleCommuneChange = async (communeId) => {
    handleChange('comuna', communeId);
    try {
      const response = await locationsAPI.getSectors(communeId);
      setSectors(response.data);
      handleChange('sector', '');
    } catch (error) {
      console.error('Error loading sectors:', error);
    }
  };

  const handleSave = () => {
    onSave(formData);
  };

  if (!property) return null;

  return (
    <Dialog open={open} onClose={onClose} maxWidth="md" fullWidth>
      <DialogTitle>
        {property.num_propiedad ? 'Editar Propiedad' : 'Nueva Propiedad'}
      </DialogTitle>
      <DialogContent>
        <Grid container spacing={2}>
          <Grid item xs={12} md={6}>
            <TextField
              fullWidth
              label="Título de la Propiedad"
              value={formData.titulo || ''}
              onChange={(e) => handleChange('titulo', e.target.value)}
              margin="normal"
            />
          </Grid>
          
          <Grid item xs={12} md={6}>
            <FormControl fullWidth margin="normal">
              <InputLabel>Tipo de Propiedad</InputLabel>
              <Select
                value={formData.tipo_propiedad || ''}
                onChange={(e) => handleChange('tipo_propiedad', e.target.value)}
              >
                <MenuItem value={1}>Casa</MenuItem>
                <MenuItem value={2}>Departamento</MenuItem>
                <MenuItem value={3}>Terreno</MenuItem>
              </Select>
            </FormControl>
          </Grid>

          <Grid item xs={12} md={3}>
            <FormControl fullWidth margin="normal">
              <InputLabel>Región</InputLabel>
              <Select
                value={formData.region || ''}
                onChange={(e) => handleRegionChange(e.target.value)}
              >
                {locations.regions.map((region) => (
                  <MenuItem key={region.idregion} value={region.idregion}>
                    {region.nombre_region}
                  </MenuItem>
                ))}
              </Select>
            </FormControl>
          </Grid>

          <Grid item xs={12} md={3}>
            <FormControl fullWidth margin="normal">
              <InputLabel>Provincia</InputLabel>
              <Select
                value={formData.provincia || ''}
                onChange={(e) => handleProvinceChange(e.target.value)}
                disabled={!formData.region}
              >
                {provinces.map((province) => (
                  <MenuItem key={province.idprovincias} value={province.idprovincias}>
                    {province.nombre_provincia}
                  </MenuItem>
                ))}
              </Select>
            </FormControl>
          </Grid>

          <Grid item xs={12} md={3}>
            <FormControl fullWidth margin="normal">
              <InputLabel>Comuna</InputLabel>
              <Select
                value={formData.comuna || ''}
                onChange={(e) => handleCommuneChange(e.target.value)}
                disabled={!formData.provincia}
              >
                {communes.map((commune) => (
                  <MenuItem key={commune.idcomunas} value={commune.idcomunas}>
                    {commune.nombre_comuna}
                  </MenuItem>
                ))}
              </Select>
            </FormControl>
          </Grid>

          <Grid item xs={12} md={3}>
            <FormControl fullWidth margin="normal">
              <InputLabel>Sector</InputLabel>
              <Select
                value={formData.sector || ''}
                onChange={(e) => handleChange('sector', e.target.value)}
                disabled={!formData.comuna}
              >
                {sectors.map((sector) => (
                  <MenuItem key={sector.idsectores} value={sector.idsectores}>
                    {sector.nombre_sector}
                  </MenuItem>
                ))}
              </Select>
            </FormControl>
          </Grid>

          <Grid item xs={12} md={6}>
            <TextField
              fullWidth
              label="Precio (CLP)"
              type="number"
              value={formData.precio_pesos || ''}
              onChange={(e) => handleChange('precio_pesos', e.target.value)}
              margin="normal"
            />
          </Grid>

          <Grid item xs={12} md={6}>
            <TextField
              fullWidth
              label="Precio (UF)"
              type="number"
              value={formData.precio_uf || ''}
              onChange={(e) => handleChange('precio_uf', e.target.value)}
              margin="normal"
            />
          </Grid>

          <Grid item xs={12} md={6}>
            <TextField
              fullWidth
              label="Área Total (m²)"
              type="number"
              value={formData.area_total || ''}
              onChange={(e) => handleChange('area_total', e.target.value)}
              margin="normal"
            />
          </Grid>

          <Grid item xs={12} md={6}>
            <TextField
              fullWidth
              label="Área Construida (m²)"
              type="number"
              value={formData.area_construida || ''}
              onChange={(e) => handleChange('area_construida', e.target.value)}
              margin="normal"
            />
          </Grid>

          <Grid item xs={12} md={6}>
            <TextField
              fullWidth
              label="Dormitorios"
              type="number"
              value={formData.cant_domitorios || ''}
              onChange={(e) => handleChange('cant_domitorios', e.target.value)}
              margin="normal"
            />
          </Grid>

          <Grid item xs={12} md={6}>
            <TextField
              fullWidth
              label="Baños"
              type="number"
              value={formData.cant_banos || ''}
              onChange={(e) => handleChange('cant_banos', e.target.value)}
              margin="normal"
            />
          </Grid>

          <Grid item xs={12}>
            <TextField
              fullWidth
              label="Descripción"
              multiline
              rows={4}
              value={formData.descripcion || ''}
              onChange={(e) => handleChange('descripcion', e.target.value)}
              margin="normal"
            />
          </Grid>

          <Grid item xs={12}>
            <Typography variant="h6" gutterBottom>Características</Typography>
            <Grid container spacing={2}>
              <Grid item xs={6} md={3}>
                <FormControlLabel
                  control={
                    <Checkbox
                      checked={formData.bodega || false}
                      onChange={(e) => handleChange('bodega', e.target.checked)}
                    />
                  }
                  label="Bodega"
                />
              </Grid>
              <Grid item xs={6} md={3}>
                <FormControlLabel
                  control={
                    <Checkbox
                      checked={formData.estacionamiento || false}
                      onChange={(e) => handleChange('estacionamiento', e.target.checked)}
                    />
                  }
                  label="Estacionamiento"
                />
              </Grid>
              <Grid item xs={6} md={3}>
                <FormControlLabel
                  control={
                    <Checkbox
                      checked={formData.logia || false}
                      onChange={(e) => handleChange('logia', e.target.checked)}
                    />
                  }
                  label="Logia"
                />
              </Grid>
              <Grid item xs={6} md={3}>
                <FormControlLabel
                  control={
                    <Checkbox
                      checked={formData.cocina_amoblada || false}
                      onChange={(e) => handleChange('cocina_amoblada', e.target.checked)}
                    />
                  }
                  label="Cocina Amoblada"
                />
              </Grid>
              <Grid item xs={6} md={3}>
                <FormControlLabel
                  control={
                    <Checkbox
                      checked={formData.antejardin || false}
                      onChange={(e) => handleChange('antejardin', e.target.checked)}
                    />
                  }
                  label="Antejardín"
                />
              </Grid>
              <Grid item xs={6} md={3}>
                <FormControlLabel
                  control={
                    <Checkbox
                      checked={formData.patio_trasero || false}
                      onChange={(e) => handleChange('patio_trasero', e.target.checked)}
                    />
                  }
                  label="Patio Trasero"
                />
              </Grid>
              <Grid item xs={6} md={3}>
                <FormControlLabel
                  control={
                    <Checkbox
                      checked={formData.piscina || false}
                      onChange={(e) => handleChange('piscina', e.target.checked)}
                    />
                  }
                  label="Piscina"
                />
              </Grid>
            </Grid>
          </Grid>
        </Grid>
      </DialogContent>
      <DialogActions>
        <Button onClick={onClose}>Cancelar</Button>
        <Button onClick={handleSave} variant="contained">Guardar</Button>
      </DialogActions>
    </Dialog>
  );
};

export default DashboardPropietario;