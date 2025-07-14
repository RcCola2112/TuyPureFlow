import React, { useState } from 'react';
import { View, Text, TextInput, StyleSheet, TouchableOpacity, Image, useWindowDimensions, Alert, ActivityIndicator } from 'react-native';

export default function ConsumerLogin({ navigation }) {
  const { width } = useWindowDimensions();
  const CARD_WIDTH = width > 400 ? 360 : width - 32;
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);

  const handleLogin = async () => {
    if (!email || !password) {
      Alert.alert('Missing Information', 'Please enter both email and password.');
      return;
    }

    setIsSubmitting(true);

    try {
      const response = await fetch('http://192.168.0.191/pureflowBackend/consumer_login.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          email: email,
          password: password,
        }),
      });

      const result = await response.json();

      if (result.success) {
        Alert.alert('Success', 'Successfully logged in!');
        navigation.navigate('ConsumerDB', { user: result.user });
      } else {
        Alert.alert('Login Failed', 'Account not found or password incorrect. You cannot enter unless your account exists in the database.');
      }
    } catch (error) {
      console.error(error);
      Alert.alert('Network Error', 'Unable to connect to the server. Please check your connection.');
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleForgotPassword = async () => {
    if (!email) {
      Alert.alert("Error", "Please enter your email in the email field above.");
      return;
    }
    try {
      const response = await fetch('http://192.168.0.191/pureflowBackend/forgot_password.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email }),
      });
      const result = await response.json();
      Alert.alert(result.success ? "Success" : "Error", result.message);
    } catch (error) {
      Alert.alert("Network Error", "Unable to connect to the server.");
    }
  };

  return (
    <View style={styles.bg}>
      <View style={styles.logoContainer}>
        <Image source={require('../assets/PureLogo.png')} style={styles.logo} resizeMode="contain" />
      </View>
      <View style={[styles.card, { width: CARD_WIDTH }]}>
        <TextInput
          style={styles.input}
          placeholder="Email or Phone Number"
          placeholderTextColor="#aaa"
          value={email}
          onChangeText={setEmail}
          keyboardType="email-address"
        />
        <TextInput
          style={styles.input}
          placeholder="Password *"
          placeholderTextColor="#aaa"
          value={password}
          onChangeText={setPassword}
          secureTextEntry
        />
        <TouchableOpacity style={styles.confirmButton} onPress={handleLogin} disabled={isSubmitting}>
          {isSubmitting ? (
            <ActivityIndicator size="small" color="#fff" />
          ) : (
            <Text style={styles.confirmButtonText}>Confirm</Text>
          )}
        </TouchableOpacity>
        <TouchableOpacity style={styles.googleButton} onPress={() => alert('Google Sign-In!')}>
          <Image source={require('../assets/Google.png')} style={styles.googleIconImg} />
          <Text style={styles.googleButtonText}>Google</Text>
        </TouchableOpacity>
        <TouchableOpacity onPress={handleForgotPassword}>
          <Text style={styles.linkText}>Forget Password</Text>
        </TouchableOpacity>
        <View style={styles.divider} />
        <TouchableOpacity onPress={() => navigation.navigate('ConsumerSignUp')}>
          <Text style={styles.linkText}>Sign Up</Text>
        </TouchableOpacity>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  bg: {
    flex: 1,
    backgroundColor: '#f8fafd',
    alignItems: 'center',
    justifyContent: 'center',
  },
  logoContainer: {
    alignItems: 'center',
    marginBottom: 18,
  },
  logo: {
    width: 110,
    height: 50,
  },
  card: {
    backgroundColor: '#fff',
    borderRadius: 18,
    padding: 24,
    alignItems: 'center',
    elevation: 4,
    shadowColor: '#3578C9',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.10,
    shadowRadius: 8,
  },
  input: {
    width: '100%',
    backgroundColor: '#f4f7fb',
    borderRadius: 8,
    paddingVertical: 13,
    paddingHorizontal: 16,
    fontSize: 16,
    marginBottom: 14,
    color: '#222',
    borderWidth: 1,
    borderColor: '#e0e0e0',
  },
  confirmButton: {
    width: '100%',
    backgroundColor: '#3FE0E8',
    borderRadius: 8,
    paddingVertical: 13,
    alignItems: 'center',
    marginBottom: 12,
    elevation: 2,
  },
  confirmButtonText: {
    color: '#fff',
    fontSize: 17,
    fontWeight: 'bold',
    letterSpacing: 0.2,
  },
  googleButton: {
    width: '100%',
    backgroundColor: '#7eeaf0',
    borderRadius: 8,
    paddingVertical: 13,
    alignItems: 'center',
    flexDirection: 'row',
    justifyContent: 'center',
    marginBottom: 12,
    elevation: 1,
  },
  googleIcon: {
    fontSize: 20,
    marginRight: 8,
  },
  googleIconImg: {
    width: 22,
    height: 22,
    marginRight: 8,
    resizeMode: 'contain',
  },
  googleButtonText: {
    color: '#fff',
    fontSize: 17,
    fontWeight: 'bold',
    letterSpacing: 0.2,
  },
  linkText: {
    color: '#888',
    fontSize: 15,
    marginVertical: 6,
    textAlign: 'center',
  },
  divider: {
    width: '100%',
    height: 1,
    backgroundColor: '#e0e0e0',
    marginVertical: 16,
  },
});
