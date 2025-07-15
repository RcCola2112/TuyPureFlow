import React, { useState } from 'react';
import { View, Text, TextInput, StyleSheet, TouchableOpacity, Image, useWindowDimensions, Alert, ActivityIndicator } from 'react-native';
import * as Location from 'expo-location';
import { useNavigation } from '@react-navigation/native';

export default function ConsumerSignUp({ navigation: propNavigation }) {
  const navigation = propNavigation || useNavigation();
  const { width } = useWindowDimensions();
  const CARD_WIDTH = width > 400 ? 360 : width - 32;
  const [step, setStep] = useState(1);
  // Step 1 fields
  const [fullName, setFullName] = useState('');
  const [contact, setContact] = useState('');
  const [email, setEmail] = useState('');
  // Step 2 fields
  const [street, setStreet] = useState('');
  const [barangay, setBarangay] = useState('');
  const [province, setProvince] = useState('');
  const [zipCode, setZipCode] = useState('');
  const [location, setLocation] = useState('');
  const [loadingLocation, setLoadingLocation] = useState(false);
  // Step 3 fields
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  const [agreeTerms, setAgreeTerms] = useState(false);
  const [agreePrivacy, setAgreePrivacy] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);

  // Progress bar width by step
  let progressWidth = '33%';
  if (step === 2) progressWidth = '66%';
  if (step === 3) progressWidth = '100%';

  // Always try to get location, don't ask for permission
  const handleChooseLocation = async () => {
    setLoadingLocation(true);
    try {
      let loc = await Location.getCurrentPositionAsync({ accuracy: Location.Accuracy.Highest });
      let addressArr = await Location.reverseGeocodeAsync({ latitude: loc.coords.latitude, longitude: loc.coords.longitude });
      if (addressArr && addressArr.length > 0) {
        const addr = addressArr[0];
        const formatted = `${addr.street || ''} ${addr.name || ''}, ${addr.subregion || ''}, ${addr.region || ''}, ${addr.country || ''}`.replace(/ +/g, ' ').trim();
        setLocation(formatted);
      } else {
        setLocation(`${loc.coords.latitude}, ${loc.coords.longitude}`);
      }
    } catch (e) {
      setLocation('Unable to fetch location');
    }
    setLoadingLocation(false);
  };

  const handleSignUp = async () => {
    // Validate all fields
    if (!fullName || !contact || !email || !street || !barangay || !province || !zipCode || !location || !password) {
      Alert.alert('Missing Information', 'Please fill out all required fields (*).');
      return;
    }
    if (password !== confirmPassword) {
      Alert.alert('Password Mismatch', 'The passwords you entered do not match.');
      return;
    }
    if (!agreeTerms || !agreePrivacy) {
      Alert.alert('Agreement Required', 'You must agree to the Terms & Conditions and Privacy Policy.');
      return;
    }

    // Parse lat/lng from location if possible
    let latitude = '';
    let longitude = '';
    const latLngMatch = location.match(/^([-+]?\d*\.?\d+),\s*([-+]?\d*\.?\d+)$/);
    if (latLngMatch) {
      latitude = latLngMatch[1];
      longitude = latLngMatch[2];
    }

    try {
      const response = await fetch('http://192.168.1.20/pureflowBackend/consumer_signup.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          name: fullName,
          email: email,
          phone: contact,
          password: password,
          street: street,
          city: barangay,
          province: province,
          zip_code: zipCode,
          latitude: latitude,
          longitude: longitude,
        }),
      });

      const result = await response.json();

      if (result.success) {
        Alert.alert('Success!', 'You have been registered successfully. Please log in.');
        navigation.navigate('ConsumerLogin');
      } else {
        Alert.alert('Sign Up Failed', result.message || 'An unknown error occurred.');
      }
    } catch (error) {
      Alert.alert('Network Error', 'Unable to connect to the server. Please check your connection and try again.');
    }
  };

  return (
    <View style={styles.bg}>
      <View style={styles.logoBar}>
        <Image source={require('../assets/PureLogo.png')} style={styles.logo} resizeMode="contain" />
      </View>
      <View style={styles.progressBarContainer}>
        <View style={styles.progressBarBg}>
          <View style={[styles.progressBarFill, { width: progressWidth }]} />
        </View>
      </View>
      <View style={[styles.card, { width: CARD_WIDTH }]}>
        {step === 1 ? (
          <>
            <Text style={styles.sectionTitle}>Personal Information</Text>
            <TextInput
              style={styles.input}
              placeholder="Full Name (Last Name, First Name Middle Initial) *"
              placeholderTextColor="#aaa"
              value={fullName}
              onChangeText={setFullName}
            />
            <TextInput
              style={styles.input}
              placeholder="Contact Number *"
              placeholderTextColor="#aaa"
              value={contact}
              onChangeText={setContact}
              keyboardType="phone-pad"
            />
            <TextInput
              style={styles.input}
              placeholder="Email Address *"
              placeholderTextColor="#aaa"
              value={email}
              onChangeText={setEmail}
              keyboardType="email-address"
            />
            <TouchableOpacity style={styles.nextButton} onPress={() => setStep(2)}>
              <Text style={styles.nextButtonText}>Next</Text>
            </TouchableOpacity>
          </>
        ) : step === 2 ? (
          <>
            <Text style={styles.sectionTitle}>Delivery Address</Text>
            <TextInput
              style={styles.input}
              placeholder="Street and House Number *"
              placeholderTextColor="#aaa"
              value={street}
              onChangeText={setStreet}
            />
            <TextInput
              style={styles.input}
              placeholder="Barangay *"
              placeholderTextColor="#aaa"
              value={barangay}
              onChangeText={setBarangay}
            />
            <TextInput
              style={styles.input}
              placeholder="Province *"
              placeholderTextColor="#aaa"
              value={province}
              onChangeText={setProvince}
            />
            <TextInput
              style={styles.input}
              placeholder="Zip Code *"
              placeholderTextColor="#aaa"
              value={zipCode}
              onChangeText={setZipCode}
            />
            <View style={styles.mapInputRow}>
              <TextInput
                style={[styles.input, { flex: 1, marginBottom: 0 }]}
                placeholder="Choose Location Using Maps *"
                placeholderTextColor="#aaa"
                value={location}
                onChangeText={setLocation}
                editable={!loadingLocation}
              />
              <TouchableOpacity style={styles.mapIconButton} onPress={handleChooseLocation} disabled={loadingLocation}>
                {loadingLocation ? (
                  <ActivityIndicator size="small" color="#3FE0E8" />
                ) : (
                  <Image source={require('../assets/Google.png')} style={styles.mapIcon} />
                )}
              </TouchableOpacity>
            </View>
            <View style={styles.buttonRow}>
              <TouchableOpacity style={styles.backButton} onPress={() => setStep(1)}>
                <Text style={styles.backButtonText}>Back</Text>
              </TouchableOpacity>
              <TouchableOpacity style={styles.nextButton} onPress={() => setStep(3)}>
                <Text style={styles.nextButtonText}>Next</Text>
              </TouchableOpacity>
            </View>
          </>
        ) : (
          <>
            <Text style={styles.sectionTitle}>Account Credentials</Text>
            <TextInput
              style={styles.input}
              placeholder="Username (Optional)"
              placeholderTextColor="#aaa"
              value={username}
              onChangeText={setUsername}
            />
            <TextInput
              style={styles.input}
              placeholder="Password *"
              placeholderTextColor="#aaa"
              value={password}
              onChangeText={setPassword}
              secureTextEntry
            />
            <TextInput
              style={styles.input}
              placeholder="Confirm Password *"
              placeholderTextColor="#aaa"
              value={confirmPassword}
              onChangeText={setConfirmPassword}
              secureTextEntry
            />
            <TouchableOpacity style={styles.checkboxRow} onPress={() => setAgreeTerms(!agreeTerms)}>
              <View style={[styles.checkbox, agreeTerms && styles.checkboxChecked]} />
              <Text style={styles.checkboxLabel}>Terms & Conditions Agreement</Text>
            </TouchableOpacity>
            <TouchableOpacity style={styles.checkboxRow} onPress={() => setAgreePrivacy(!agreePrivacy)}>
              <View style={[styles.checkbox, agreePrivacy && styles.checkboxChecked]} />
              <Text style={styles.checkboxLabel}>Privacy Policy Agreement</Text>
            </TouchableOpacity>
            <View style={styles.buttonRow}>
              <TouchableOpacity style={styles.backButton} onPress={() => setStep(2)}>
                <Text style={styles.backButtonText}>Back</Text>
              </TouchableOpacity>
              <TouchableOpacity
                style={[styles.nextButton, { backgroundColor: '#3FE0E8' }]}
                onPress={handleSignUp}
              >
                <Text style={styles.nextButtonText}>Finish</Text>
              </TouchableOpacity>
            </View>
          </>
        )}
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  bg: {
    flex: 1,
    backgroundColor: '#fff',
    alignItems: 'center',
    paddingTop: 30,
  },
  logoBar: {
    width: '100%',
    alignItems: 'flex-start',
    paddingLeft: 18,
    marginBottom: 18,
  },
  logo: {
    width: 110,
    height: 50,
  },
  progressBarContainer: {
    width: '100%',
    maxWidth: 600,
    alignSelf: 'center',
    marginBottom: 30,
    paddingHorizontal: 18,
  },
  progressBarBg: {
    width: '100%',
    height: 10,
    backgroundColor: '#ccc',
    borderRadius: 8,
    overflow: 'hidden',
  },
  progressBarFill: {
    height: '100%',
    backgroundColor: '#3FE0E8',
    borderRadius: 8,
  },
  card: {
    backgroundColor: '#fafbfc',
    borderRadius: 18,
    padding: 24,
    alignItems: 'center',
    elevation: 2,
    shadowColor: '#3578C9',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.10,
    shadowRadius: 8,
  },
  sectionTitle: {
    fontSize: 22,
    fontWeight: 'bold',
    color: '#222',
    marginBottom: 18,
    alignSelf: 'flex-start',
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
  mapInputRow: {
    flexDirection: 'row',
    alignItems: 'center',
    width: '100%',
    marginBottom: 14,
  },
  mapIconButton: {
    marginLeft: -38,
    padding: 8,
    zIndex: 2,
  },
  mapIcon: {
    width: 28,
    height: 28,
    resizeMode: 'contain',
  },
  buttonRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    width: '100%',
    marginTop: 10,
    alignItems: 'center',
  },
  backButton: {
    width: 140,
    height: 48,
    backgroundColor: '#3FE0E8',
    borderRadius: 8,
    alignItems: 'center',
    justifyContent: 'center',
    elevation: 2,
  },
  backButtonText: {
    color: '#fff',
    fontSize: 17,
    fontWeight: 'bold',
    letterSpacing: 0.2,
  },
  nextButton: {
    width: 140,
    height: 48,
    backgroundColor: '#3FE0E8',
    borderRadius: 8,
    alignItems: 'center',
    justifyContent: 'center',
    elevation: 2,
  },
  nextButtonText: {
    color: '#fff',
    fontSize: 17,
    fontWeight: 'bold',
    letterSpacing: 0.2,
  },
  checkboxRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 14,
    width: '100%',
  },
  checkbox: {
    width: 22,
    height: 22,
    borderWidth: 1.5,
    borderColor: '#222',
    borderRadius: 4,
    marginRight: 12,
    backgroundColor: '#fff',
  },
  checkboxChecked: {
    backgroundColor: '#3FE0E8',
    borderColor: '#3FE0E8',
  },
  checkboxLabel: {
    fontSize: 16,
    color: '#222',
  },
}); 