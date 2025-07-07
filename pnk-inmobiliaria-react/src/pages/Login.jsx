import React from 'react';
import { Container } from '@mui/material';
import LoginForm from '../components/LoginForm';

const Login = () => {
  return (
    <Container maxWidth="sm" sx={{ py: 4 }}>
      <LoginForm />
    </Container>
  );
};

export default Login;