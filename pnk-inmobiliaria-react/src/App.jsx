import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import { ThemeProvider, createTheme } from '@mui/material/styles';
import CssBaseline from '@mui/material/CssBaseline';
import { AuthProvider } from './contexts/AuthContext';
import Header from './components/Header';
import Footer from './components/Footer';
import Home from './pages/Home';
import Login from './pages/Login';
import RegisterPropietario from './pages/RegisterPropietario';
import RegisterGestor from './pages/RegisterGestor';
import Dashboard from './pages/Dashboard';
import DashboardPropietario from './pages/DashboardPropietario';
import PropertyDetail from './pages/PropertyDetail';
import RecoverPassword from './pages/RecoverPassword';
import './App.css';

const theme = createTheme({
  palette: {
    primary: {
      main: '#9c27b0',
    },
    secondary: {
      main: '#7e57c2',
    },
  },
});

function App() {
  return (
    <ThemeProvider theme={theme}>
      <CssBaseline />
      <AuthProvider>
        <Router>
          <div className="App">
            <Header />
            <main className="main-content">
              <Routes>
                <Route path="/" element={<Home />} />
                <Route path="/login" element={<Login />} />
                <Route path="/register-propietario" element={<RegisterPropietario />} />
                <Route path="/register-gestor" element={<RegisterGestor />} />
                <Route path="/dashboard" element={<Dashboard />} />
                <Route path="/dashboard-propietario" element={<DashboardPropietario />} />
                <Route path="/property/:id" element={<PropertyDetail />} />
                <Route path="/recover-password" element={<RecoverPassword />} />
              </Routes>
            </main>
            <Footer />
          </div>
        </Router>
      </AuthProvider>
    </ThemeProvider>
  );
}

export default App;