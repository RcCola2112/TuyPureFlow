import React, { useState } from 'react';
import { View, Text, TextInput, TouchableOpacity, StyleSheet, Image } from 'react-native';

// Set your backend base URL here
const BASE_URL = 'http://192.168.1.20/pureflowBackend';

export default function DistributorLoginScreen({ navigation }) {
  const [emailOrPhone, setEmailOrPhone] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  const handleLogin = async () => {
    setLoading(true);
    setError('');
    try {
      const response = await fetch(`${BASE_URL}/distributor_login.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email_or_phone: emailOrPhone, password }),
      });
      const result = await response.json();
      if (result.success) {
        navigation.replace('DistributorDB', { distributor: result.distributor });
      } else {
        setError(result.message || 'Login failed.');
      }
    } catch (e) {
      setError('Network error.');
    }
    setLoading(false);
  };

  return (
    <View style={styles.container}>
      <Image source={require('../assets/PureLogo.png')} style={styles.logo} resizeMode="contain" />
      <Text style={styles.title}>Distributor Login</Text>
      <TextInput
        style={styles.input}
        placeholder="Email or Phone"
        value={emailOrPhone}
        onChangeText={setEmailOrPhone}
        autoCapitalize="none"
        keyboardType="email-address"
      />
      <TextInput
        style={styles.input}
        placeholder="Password"
        value={password}
        onChangeText={setPassword}
        secureTextEntry
      />
      {error ? <Text style={styles.error}>{error}</Text> : null}
      <TouchableOpacity style={styles.button} onPress={handleLogin} disabled={loading}>
        <Text style={styles.buttonText}>{loading ? 'Logging in...' : 'Login'}</Text>
      </TouchableOpacity>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
    alignItems: 'center',
    justifyContent: 'center',
    padding: 24,
  },
  logo: {
    width: 120,
    height: 60,
    marginBottom: 24,
  },
  title: {
    fontSize: 24,
    fontWeight: 'bold',
    marginBottom: 24,
    color: '#3578C9',
  },
  input: {
    width: '100%',
    maxWidth: 350,
    backgroundColor: '#F7F7F7',
    borderRadius: 8,
    paddingVertical: 12,
    paddingHorizontal: 14,
    fontSize: 16,
    marginBottom: 16,
    color: '#222',
    borderBottomWidth: 1,
    borderBottomColor: '#e0e0e0',
  },
  button: {
    backgroundColor: '#3FE0E8',
    borderRadius: 8,
    paddingVertical: 13,
    paddingHorizontal: 60,
    alignSelf: 'center',
    elevation: 2,
    marginTop: 10,
  },
  buttonText: {
    color: '#fff',
    fontSize: 17,
    fontWeight: 'bold',
    letterSpacing: 0.2,
    textAlign: 'center',
  },
  error: {
    color: 'red',
    marginBottom: 10,
    textAlign: 'center',
  },
}); 