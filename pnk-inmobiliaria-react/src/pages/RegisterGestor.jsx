import React, { useState } from 'react';
import { 
  Container, 
  Paper, 
  TextField, 
  Button, 
  Typography, 
  Box,
  FormControl,
  InputLabel,
  Select,
  MenuItem,
  Alert
} from '@mui/material';
import { useAuth } from '../contexts/AuthContext';
import { useNavigate, Link } from 'react-router-dom';
import Swal from 'sweetalert2';

const RegisterGestor = () => {
  const [formData, setFormData] = useState({
    rut: '',
    nombre_completo: '',
    fecha_nacimiento: '',
    correo: '',
    password: '',
    password2: '',
    sexo: '',
    telefono: '',
    certificado: null
  });
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);
  const { register } = useAuth();
  const navigate = useNavigate();

  const handleChange = (e) => {
    if (e.target.name === 'certificado') {
      setFormData({
        ...formData,
        [e.target.name]: e.target.files[0]
      });
    } else {
      setFormData({
        ...formData,
        [e.target.name]: e.target.value
      });
    }
  };

  const validarRut = (rut) => {
    if (!/^[0-9]+-[0-9kK]{1}$/.test(rut)) return false;
    const tmp = rut.split('-');
    const digv = tmp[1].toLowerCase();
    const rutNum = tmp[0];
    
    let suma = 0;
    let multiplo = 2;
    
    for (let i = rutNum.length - 1; i >= 0; i--) {
      suma += parseInt(rutNum.charAt(i)) * multiplo;
      multiplo = multiplo === 7 ? 2 : multiplo + 1;
    }
    
    const dvCalculado = 11 - (suma % 11);
    let dv = dvCalculado === 11 ? '0' : dvCalculado === 10 ? 'k' : dvCalculado.toString();
    
    return dv === digv;
  };

  const validarPassword = (password) => {
    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*])(?=.{8,})/;
    return regex.test(password);
  };

  const validarEmail = (email) => {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
  };

  const validarTelefono = (telefono) => {
    const regex = /^\+569\d{8}$/;
    return regex.test(telefono);
  };

  const validarFechaNacimiento = (fecha) => {
    const fechaSeleccionada = new Date(fecha);
    const hoy = new Date();
    return fechaSeleccionada <= hoy;
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setError('');

    // Validaciones
    const { rut, nombre_completo, fecha_nacimiento, correo, password, password2, sexo, telefono, certificado } = formData;

    if (!rut || !nombre_completo || !fecha_nacimiento || !correo || !password || !password2 || !sexo || !telefono || !certificado) {
      setError('Todos los campos son obligatorios');
      setLoading(false);
      return;
    }

    if (!validarRut(rut)) {
      setError('El RUT ingresado no es válido');
      setLoading(false);
      return;
    }

    if (!validarFechaNacimiento(fecha_nacimiento)) {
      setError('La fecha de nacimiento no puede ser una fecha futura');
      setLoading(false);
      return;
    }

    if (!validarEmail(correo)) {
      setError('El correo electrónico no es válido');
      setLoading(false);
      return;
    }

    if (!validarPassword(password)) {
      setError('La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula y un carácter especial');
      setLoading(false);
      return;
    }

    if (password !== password2) {
      setError('Las contraseñas no coinciden');
      setLoading(false);
      return;
    }

    if (!validarTelefono(telefono)) {
      setError('El teléfono debe tener el formato +569XXXXXXXX');
      setLoading(false);
      return;
    }

    // Validar extensión del certificado
    const extensionesPermitidas = ['pdf', 'jpg', 'jpeg', 'png'];
    const extension = certificado.name.split('.').pop().toLowerCase();
    if (!extensionesPermitidas.includes(extension)) {
      setError('El certificado debe ser un archivo PDF, JPG, JPEG o PNG');
      setLoading(false);
      return;
    }

    try {
      const result = await register(formData, 'gestor');
      if (result.success) {
        await Swal.fire({
          icon: 'success',
          title: '¡Éxito!',
          text: result.message,
          confirmButtonColor: '#9c27b0'
        });
        navigate('/');
      } else {
        setError(result.message);
      }
    } catch (error) {
      setError('Error al registrar gestor');
    } finally {
      setLoading(false);
    }
  };

  // Establecer fecha máxima como hoy
  const today = new Date().toISOString().split('T')[0];

  return (
    <Container maxWidth="sm" sx={{ py: 4 }}>
      <Paper elevation={3} sx={{ p: 4, borderRadius: 3 }}>
        <Typography variant="h4" align="center" gutterBottom sx={{ color: '#9c27b0', fontWeight: 'bold' }}>
          Registro de Gestor Inmobiliario Free
        </Typography>

        {error && (
          <Alert severity="error" sx={{ mb: 2 }}>
            {error}
          </Alert>
        )}

        <form onSubmit={handleSubmit}>
          <TextField
            fullWidth
            label="RUN"
            name="rut"
            value={formData.rut}
            onChange={handleChange}
            margin="normal"
            required
            placeholder="12345678-9"
          />

          <TextField
            fullWidth
            label="Nombre Completo"
            name="nombre_completo"
            value={formData.nombre_completo}
            onChange={handleChange}
            margin="normal"
            required
          />

          <TextField
            fullWidth
            label="Fecha de Nacimiento"
            name="fecha_nacimiento"
            type="date"
            value={formData.fecha_nacimiento}
            onChange={handleChange}
            margin="normal"
            required
            InputLabelProps={{ shrink: true }}
            inputProps={{ max: today }}
          />

          <TextField
            fullWidth
            label="Correo Electrónico"
            name="correo"
            type="email"
            value={formData.correo}
            onChange={handleChange}
            margin="normal"
            required
          />

          <TextField
            fullWidth
            label="Contraseña"
            name="password"
            type="password"
            value={formData.password}
            onChange={handleChange}
            margin="normal"
            required
            helperText="Mínimo 8 caracteres, una mayúscula, una minúscula y un carácter especial"
          />

          <TextField
            fullWidth
            label="Confirmar Contraseña"
            name="password2"
            type="password"
            value={formData.password2}
            onChange={handleChange}
            margin="normal"
            required
          />

          <FormControl fullWidth margin="normal" required>
            <InputLabel>Sexo</InputLabel>
            <Select
              name="sexo"
              value={formData.sexo}
              onChange={handleChange}
            >
              <MenuItem value="">Seleccione...</MenuItem>
              <MenuItem value="M">Masculino</MenuItem>
              <MenuItem value="F">Femenino</MenuItem>
              <MenuItem value="O">Otro</MenuItem>
            </Select>
          </FormControl>

          <TextField
            fullWidth
            label="Teléfono"
            name="telefono"
            value={formData.telefono}
            onChange={handleChange}
            margin="normal"
            required
            placeholder="+56912345678"
          />

          <Box sx={{ mt: 2, mb: 2 }}>
            <Typography variant="body2" gutterBottom>
              Certificado de Antecedentes:
            </Typography>
            <input
              type="file"
              name="certificado"
              onChange={handleChange}
              accept=".pdf,.jpg,.jpeg,.png"
              required
              style={{ width: '100%', padding: '8px' }}
            />
            <Typography variant="caption" color="text.secondary">
              Formatos permitidos: PDF, JPG, JPEG, PNG
            </Typography>
          </Box>

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
            {loading ? 'Registrando...' : 'Registrarse'}
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

export default RegisterGestor;