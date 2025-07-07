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
  MenuItem
} from '@mui/material';
import { Edit, Delete, Home, Person } from '@mui/icons-material';
import { useAuth } from '../contexts/AuthContext';
import { usersAPI, gestoresAPI, propietariosAPI, propertiesAPI } from '../services/api';
import { useNavigate } from 'react-router-dom';
import Swal from 'sweetalert2';

const Dashboard = () => {
  const { user } = useAuth();
  const navigate = useNavigate();
  const [activeTab, setActiveTab] = useState('usuarios');
  const [data, setData] = useState([]);
  const [loading, setLoading] = useState(true);
  const [editDialog, setEditDialog] = useState({ open: false, item: null, type: '' });

  useEffect(() => {
    if (!user || user.tipo !== 'admin') {
      navigate('/');
      return;
    }
    loadData();
  }, [user, navigate, activeTab]);

  const loadData = async () => {
    setLoading(true);
    try {
      let response;
      switch (activeTab) {
        case 'usuarios':
          response = await usersAPI.getAll();
          break;
        case 'gestores':
          response = await gestoresAPI.getAll();
          break;
        case 'propietarios':
          response = await propietariosAPI.getAll();
          break;
        case 'propiedades':
          response = await propertiesAPI.getAll();
          break;
        default:
          response = { data: [] };
      }
      setData(response.data);
    } catch (error) {
      console.error('Error loading data:', error);
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Error al cargar los datos'
      });
    } finally {
      setLoading(false);
    }
  };

  const handleEdit = (item) => {
    setEditDialog({ open: true, item, type: activeTab });
  };

  const handleDelete = async (id) => {
    const result = await Swal.fire({
      title: '¿Estás seguro?',
      text: 'Esta acción no se puede deshacer',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    });

    if (result.isConfirmed) {
      try {
        switch (activeTab) {
          case 'usuarios':
            await usersAPI.delete(id);
            break;
          case 'gestores':
            await gestoresAPI.delete(id);
            break;
          case 'propietarios':
            await propietariosAPI.delete(id);
            break;
          case 'propiedades':
            await propertiesAPI.delete(id);
            break;
        }
        
        await Swal.fire({
          icon: 'success',
          title: '¡Eliminado!',
          text: 'El elemento ha sido eliminado correctamente'
        });
        
        loadData();
      } catch (error) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Error al eliminar el elemento'
        });
      }
    }
  };

  const handleSaveEdit = async (editedItem) => {
    try {
      switch (editDialog.type) {
        case 'usuarios':
          await usersAPI.update(editedItem.id, editedItem);
          break;
        case 'gestores':
          await gestoresAPI.update(editedItem.id, editedItem);
          break;
        case 'propietarios':
          await propietariosAPI.update(editedItem.id, editedItem);
          break;
        case 'propiedades':
          await propertiesAPI.update(editedItem.num_propiedad, editedItem);
          break;
      }
      
      setEditDialog({ open: false, item: null, type: '' });
      await Swal.fire({
        icon: 'success',
        title: '¡Actualizado!',
        text: 'Los datos han sido actualizados correctamente'
      });
      
      loadData();
    } catch (error) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Error al actualizar los datos'
      });
    }
  };

  const renderTable = () => {
    if (loading) return <Typography>Cargando...</Typography>;

    switch (activeTab) {
      case 'usuarios':
        return (
          <TableContainer component={Paper}>
            <Table>
              <TableHead>
                <TableRow>
                  <TableCell>RUT</TableCell>
                  <TableCell>Nombres</TableCell>
                  <TableCell>Apellido Paterno</TableCell>
                  <TableCell>Apellido Materno</TableCell>
                  <TableCell>Usuario</TableCell>
                  <TableCell>Estado</TableCell>
                  <TableCell>Acciones</TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {data.map((user) => (
                  <TableRow key={user.id}>
                    <TableCell>{user.rut}</TableCell>
                    <TableCell>{user.nombres}</TableCell>
                    <TableCell>{user.ap_paterno}</TableCell>
                    <TableCell>{user.ap_materno}</TableCell>
                    <TableCell>{user.usuario}</TableCell>
                    <TableCell>{user.estado === 1 ? 'Activo' : 'Inactivo'}</TableCell>
                    <TableCell>
                      <IconButton onClick={() => handleEdit(user)} color="primary">
                        <Edit />
                      </IconButton>
                      <IconButton onClick={() => handleDelete(user.id)} color="error">
                        <Delete />
                      </IconButton>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </TableContainer>
        );

      case 'gestores':
        return (
          <TableContainer component={Paper}>
            <Table>
              <TableHead>
                <TableRow>
                  <TableCell>RUT</TableCell>
                  <TableCell>Nombre Completo</TableCell>
                  <TableCell>Fecha Nacimiento</TableCell>
                  <TableCell>Correo</TableCell>
                  <TableCell>Sexo</TableCell>
                  <TableCell>Teléfono</TableCell>
                  <TableCell>Estado</TableCell>
                  <TableCell>Acciones</TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {data.map((gestor) => (
                  <TableRow key={gestor.id}>
                    <TableCell>{gestor.rut}</TableCell>
                    <TableCell>{gestor.nombre_completo}</TableCell>
                    <TableCell>{gestor.fecha_nacimiento}</TableCell>
                    <TableCell>{gestor.correo}</TableCell>
                    <TableCell>{gestor.sexo}</TableCell>
                    <TableCell>{gestor.telefono}</TableCell>
                    <TableCell>{gestor.estado === 1 ? 'Activo' : 'Inactivo'}</TableCell>
                    <TableCell>
                      <IconButton onClick={() => handleEdit(gestor)} color="primary">
                        <Edit />
                      </IconButton>
                      <IconButton onClick={() => handleDelete(gestor.id)} color="error">
                        <Delete />
                      </IconButton>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </TableContainer>
        );

      case 'propietarios':
        return (
          <TableContainer component={Paper}>
            <Table>
              <TableHead>
                <TableRow>
                  <TableCell>RUT</TableCell>
                  <TableCell>Nombre Completo</TableCell>
                  <TableCell>Fecha Nacimiento</TableCell>
                  <TableCell>Correo</TableCell>
                  <TableCell>Sexo</TableCell>
                  <TableCell>Teléfono</TableCell>
                  <TableCell>Acciones</TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {data.map((propietario) => (
                  <TableRow key={propietario.id}>
                    <TableCell>{propietario.rut}</TableCell>
                    <TableCell>{propietario.nombre_completo}</TableCell>
                    <TableCell>{propietario.fecha_nacimiento}</TableCell>
                    <TableCell>{propietario.correo}</TableCell>
                    <TableCell>{propietario.sexo}</TableCell>
                    <TableCell>{propietario.telefono}</TableCell>
                    <TableCell>
                      <IconButton onClick={() => handleEdit(propietario)} color="primary">
                        <Edit />
                      </IconButton>
                      <IconButton onClick={() => handleDelete(propietario.id)} color="error">
                        <Delete />
                      </IconButton>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </TableContainer>
        );

      case 'propiedades':
        return (
          <TableContainer component={Paper}>
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
                {data.map((propiedad) => (
                  <TableRow key={propiedad.num_propiedad}>
                    <TableCell>{propiedad.num_propiedad}</TableCell>
                    <TableCell>{propiedad.titulopropiedad}</TableCell>
                    <TableCell>{propiedad.tipo}</TableCell>
                    <TableCell>{propiedad.precio_pesos?.toLocaleString('es-CL')}</TableCell>
                    <TableCell>{propiedad.precio_uf?.toLocaleString('es-CL')}</TableCell>
                    <TableCell>{propiedad.estado === 1 ? 'Activo' : 'Inactivo'}</TableCell>
                    <TableCell>
                      <IconButton onClick={() => handleEdit(propiedad)} color="primary">
                        <Edit />
                      </IconButton>
                      <IconButton onClick={() => handleDelete(propiedad.num_propiedad)} color="error">
                        <Delete />
                      </IconButton>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </TableContainer>
        );

      default:
        return null;
    }
  };

  return (
    <Container maxWidth="xl" sx={{ py: 4 }}>
      <Typography variant="h3" gutterBottom sx={{ color: '#9c27b0', fontWeight: 'bold' }}>
        Panel de Administración
      </Typography>

      <Grid container spacing={3} sx={{ mb: 4 }}>
        <Grid item xs={12} sm={6} md={3}>
          <Card 
            sx={{ 
              cursor: 'pointer',
              backgroundColor: activeTab === 'usuarios' ? '#9c27b0' : 'white',
              color: activeTab === 'usuarios' ? 'white' : 'inherit'
            }}
            onClick={() => setActiveTab('usuarios')}
          >
            <CardContent sx={{ textAlign: 'center' }}>
              <Person sx={{ fontSize: 40, mb: 1 }} />
              <Typography variant="h6">Usuarios</Typography>
            </CardContent>
          </Card>
        </Grid>

        <Grid item xs={12} sm={6} md={3}>
          <Card 
            sx={{ 
              cursor: 'pointer',
              backgroundColor: activeTab === 'gestores' ? '#9c27b0' : 'white',
              color: activeTab === 'gestores' ? 'white' : 'inherit'
            }}
            onClick={() => setActiveTab('gestores')}
          >
            <CardContent sx={{ textAlign: 'center' }}>
              <Person sx={{ fontSize: 40, mb: 1 }} />
              <Typography variant="h6">Gestores</Typography>
            </CardContent>
          </Card>
        </Grid>

        <Grid item xs={12} sm={6} md={3}>
          <Card 
            sx={{ 
              cursor: 'pointer',
              backgroundColor: activeTab === 'propietarios' ? '#9c27b0' : 'white',
              color: activeTab === 'propietarios' ? 'white' : 'inherit'
            }}
            onClick={() => setActiveTab('propietarios')}
          >
            <CardContent sx={{ textAlign: 'center' }}>
              <Person sx={{ fontSize: 40, mb: 1 }} />
              <Typography variant="h6">Propietarios</Typography>
            </CardContent>
          </Card>
        </Grid>

        <Grid item xs={12} sm={6} md={3}>
          <Card 
            sx={{ 
              cursor: 'pointer',
              backgroundColor: activeTab === 'propiedades' ? '#9c27b0' : 'white',
              color: activeTab === 'propiedades' ? 'white' : 'inherit'
            }}
            onClick={() => setActiveTab('propiedades')}
          >
            <CardContent sx={{ textAlign: 'center' }}>
              <Home sx={{ fontSize: 40, mb: 1 }} />
              <Typography variant="h6">Propiedades</Typography>
            </CardContent>
          </Card>
        </Grid>
      </Grid>

      <Paper sx={{ p: 3 }}>
        <Typography variant="h5" gutterBottom sx={{ textTransform: 'capitalize' }}>
          Gestión de {activeTab}
        </Typography>
        {renderTable()}
      </Paper>

      {/* Dialog de edición */}
      <EditDialog 
        open={editDialog.open}
        item={editDialog.item}
        type={editDialog.type}
        onClose={() => setEditDialog({ open: false, item: null, type: '' })}
        onSave={handleSaveEdit}
      />
    </Container>
  );
};

// Componente para el diálogo de edición
const EditDialog = ({ open, item, type, onClose, onSave }) => {
  const [editedItem, setEditedItem] = useState({});

  useEffect(() => {
    if (item) {
      setEditedItem({ ...item });
    }
  }, [item]);

  const handleChange = (field, value) => {
    setEditedItem(prev => ({ ...prev, [field]: value }));
  };

  const handleSave = () => {
    onSave(editedItem);
  };

  if (!item) return null;

  return (
    <Dialog open={open} onClose={onClose} maxWidth="md" fullWidth>
      <DialogTitle>Editar {type}</DialogTitle>
      <DialogContent>
        {type === 'usuarios' && (
          <>
            <TextField
              fullWidth
              label="RUT"
              value={editedItem.rut || ''}
              onChange={(e) => handleChange('rut', e.target.value)}
              margin="normal"
            />
            <TextField
              fullWidth
              label="Nombres"
              value={editedItem.nombres || ''}
              onChange={(e) => handleChange('nombres', e.target.value)}
              margin="normal"
            />
            <TextField
              fullWidth
              label="Apellido Paterno"
              value={editedItem.ap_paterno || ''}
              onChange={(e) => handleChange('ap_paterno', e.target.value)}
              margin="normal"
            />
            <TextField
              fullWidth
              label="Apellido Materno"
              value={editedItem.ap_materno || ''}
              onChange={(e) => handleChange('ap_materno', e.target.value)}
              margin="normal"
            />
            <TextField
              fullWidth
              label="Usuario"
              value={editedItem.usuario || ''}
              onChange={(e) => handleChange('usuario', e.target.value)}
              margin="normal"
            />
            <FormControl fullWidth margin="normal">
              <InputLabel>Estado</InputLabel>
              <Select
                value={editedItem.estado || 1}
                onChange={(e) => handleChange('estado', e.target.value)}
              >
                <MenuItem value={1}>Activo</MenuItem>
                <MenuItem value={0}>Inactivo</MenuItem>
              </Select>
            </FormControl>
          </>
        )}

        {(type === 'gestores' || type === 'propietarios') && (
          <>
            <TextField
              fullWidth
              label="RUT"
              value={editedItem.rut || ''}
              onChange={(e) => handleChange('rut', e.target.value)}
              margin="normal"
            />
            <TextField
              fullWidth
              label="Nombre Completo"
              value={editedItem.nombre_completo || ''}
              onChange={(e) => handleChange('nombre_completo', e.target.value)}
              margin="normal"
            />
            <TextField
              fullWidth
              label="Fecha de Nacimiento"
              type="date"
              value={editedItem.fecha_nacimiento || ''}
              onChange={(e) => handleChange('fecha_nacimiento', e.target.value)}
              margin="normal"
              InputLabelProps={{ shrink: true }}
            />
            <TextField
              fullWidth
              label="Correo"
              value={editedItem.correo || ''}
              onChange={(e) => handleChange('correo', e.target.value)}
              margin="normal"
            />
            <FormControl fullWidth margin="normal">
              <InputLabel>Sexo</InputLabel>
              <Select
                value={editedItem.sexo || ''}
                onChange={(e) => handleChange('sexo', e.target.value)}
              >
                <MenuItem value="M">Masculino</MenuItem>
                <MenuItem value="F">Femenino</MenuItem>
                <MenuItem value="O">Otro</MenuItem>
              </Select>
            </FormControl>
            <TextField
              fullWidth
              label="Teléfono"
              value={editedItem.telefono || ''}
              onChange={(e) => handleChange('telefono', e.target.value)}
              margin="normal"
            />
            {type === 'gestores' && (
              <FormControl fullWidth margin="normal">
                <InputLabel>Estado</InputLabel>
                <Select
                  value={editedItem.estado || 1}
                  onChange={(e) => handleChange('estado', e.target.value)}
                >
                  <MenuItem value={1}>Activo</MenuItem>
                  <MenuItem value={0}>Inactivo</MenuItem>
                </Select>
              </FormControl>
            )}
          </>
        )}
      </DialogContent>
      <DialogActions>
        <Button onClick={onClose}>Cancelar</Button>
        <Button onClick={handleSave} variant="contained">Guardar</Button>
      </DialogActions>
    </Dialog>
  );
};

export default Dashboard;