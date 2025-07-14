import React, { useState } from 'react';
import { View, Text, TextInput, StyleSheet, TouchableOpacity, Image, Dimensions, Modal, Platform, ScrollView } from 'react-native';
import { Picker } from '@react-native-picker/picker';
import * as Location from 'expo-location';
import MapView, { Marker } from 'react-native-maps';
import * as DocumentPicker from 'expo-document-picker';
import CheckBox from 'react-native-check-box';

const { width } = Dimensions.get('window');
const CARD_MAX_WIDTH = width > 500 ? 500 : width - 32;

// Set your backend base URL here using your IPv4 address and correct folder
const API_BASE_URL = 'http://192.168.0.191/pureflowBackend';

export default function Login({ navigation }) {
  const [step, setStep] = useState(1);
  // Step 1 fields
  const [businessName, setBusinessName] = useState('');
  const [ownerName, setOwnerName] = useState('');
  const [contactNumber, setContactNumber] = useState('');
  const [email, setEmail] = useState('');
  const [operatingHours, setOperatingHours] = useState('');
  // Step 2 fields
  const [street, setStreet] = useState('');
  const [city, setCity] = useState('');
  const [region, setRegion] = useState('');
  const [location, setLocation] = useState('');
  const [locationCoords, setLocationCoords] = useState(null);
  const [locationLoading, setLocationLoading] = useState(false);
  // Step 3 fields
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  // Step 4 fields
  const [permitFile, setPermitFile] = useState(null);
  const [idFile, setIdFile] = useState(null);
  const [addressFile, setAddressFile] = useState(null);
  const [agreeTerms, setAgreeTerms] = useState(false);
  const [agreePrivacy, setAgreePrivacy] = useState(false);

  // Example data for dropdowns
  const cities = ['Manila', 'Quezon City', 'Cebu', 'Davao'];
  const regions = ['NCR', 'Region I', 'Region II', 'Region III'];

  const getCurrentLocationAndSet = async () => {
    setLocationLoading(true);
    let { status } = await Location.requestForegroundPermissionsAsync();
    if (status !== 'granted') {
      alert('Permission to access location was denied');
      setLocationLoading(false);
      return;
    }
    try {
      let userLocation = await Location.getCurrentPositionAsync({});
      setLocationCoords({
        latitude: userLocation.coords.latitude,
        longitude: userLocation.coords.longitude,
      });
      // Reverse geocode to get address
      const results = await Location.reverseGeocodeAsync({
        latitude: userLocation.coords.latitude,
        longitude: userLocation.coords.longitude,
      });
      if (results && results.length > 0) {
        const addr = results[0];
        const addressString = `${addr.street || ''} ${addr.name || ''}, ${addr.city || ''}, ${addr.region || ''}`.trim();
        setLocation(addressString || `${userLocation.coords.latitude.toFixed(5)}, ${userLocation.coords.longitude.toFixed(5)}`);
      } else {
        setLocation(`${userLocation.coords.latitude.toFixed(5)}, ${userLocation.coords.longitude.toFixed(5)}`);
      }
    } catch (e) {
      alert('Failed to get location');
    }
    setLocationLoading(false);
  };

  const pickFile = async (setter) => {
    let result = await DocumentPicker.getDocumentAsync({ type: '*/*' });
    if (!result.canceled && result.assets && result.assets.length > 0) {
      setter(result.assets[0]);
    } else {
      setter(null);
    }
  };

  const handleSubmit = async () => {
    if (!agreeTerms || !agreePrivacy) {
      alert('You must agree to the Terms & Conditions and Privacy Policy.');
      return;
    }
    const payload = {
      business_name: businessName,
      owner_name: ownerName,
      contact_number: contactNumber,
      email: email,
      operating_hours: operatingHours,
      street: street,
      city: city,
      region: region,
      zip_code: '1000',
      latitude: locationCoords ? String(locationCoords.latitude) : '',
      longitude: locationCoords ? String(locationCoords.longitude) : '',
      username: username,
      password: password,
      // If you want to send document fields, add them here as base64 or URLs
    };
    try {
      const response = await fetch(`${API_BASE_URL}/distributor_signup.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
      });
      const result = await response.json();
      if (result.success) {
        alert('Registration successful!');
        navigation.navigate('DistributorDB');
      } else {
        alert(result.message || 'Registration failed.');
      }
    } catch (error) {
      alert('An error occurred: ' + error.message);
      console.log(error);
    }
  };

  return (
    <ScrollView contentContainerStyle={styles.container}>
      {/* Logo */}
      <View style={styles.logoContainer}>
        <Image source={require('../assets/PureLogo.png')} style={styles.logo} resizeMode="contain" />
      </View>
      {/* Progress Bar */}
      <Text style={styles.progressLabel}>Progress Bar</Text>
      <View style={styles.progressBarBg}>
        <View style={[styles.progressBarFill, { width: step === 1 ? '25%' : step === 2 ? '50%' : step === 3 ? '75%' : '100%' }]} />
      </View>
      {step === 1 ? (
        <>
          {/* Business Information Form */}
          <Text style={styles.formTitle}>Business Information</Text>
          <View style={styles.formCard}>
            <TextInput
              style={styles.input}
              placeholder="Business Name *"
              placeholderTextColor="#aaa"
              value={businessName}
              onChangeText={setBusinessName}
            />
            <TextInput
              style={styles.input}
              placeholder="Owner's Name *"
              placeholderTextColor="#aaa"
              value={ownerName}
              onChangeText={setOwnerName}
            />
            <TextInput
              style={styles.input}
              placeholder="Contact Number *"
              placeholderTextColor="#aaa"
              value={contactNumber}
              onChangeText={setContactNumber}
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
            <TextInput
              style={styles.input}
              placeholder="Operating Hours *"
              placeholderTextColor="#aaa"
              value={operatingHours}
              onChangeText={setOperatingHours}
            />
          </View>
          {/* Next Button */}
          <TouchableOpacity
            style={[styles.nextButton, (!businessName || !ownerName || !contactNumber || !email || !operatingHours) ? { opacity: 0.5 } : {}]}
            onPress={() => setStep(2)}
            disabled={!businessName || !ownerName || !contactNumber || !email || !operatingHours}
          >
            <Text style={styles.nextButtonText}>Next</Text>
          </TouchableOpacity>
        </>
      ) : step === 2 ? (
        <>
          {/* Business Address Form */}
          <Text style={styles.formTitle}>Business Address</Text>
          <View style={styles.formCard}>
            <TextInput
              style={styles.input}
              placeholder="Street and House Number *"
              placeholderTextColor="#aaa"
              value={street}
              onChangeText={setStreet}
            />
            <View style={styles.pickerWrapper}>
              <Picker
                selectedValue={city}
                onValueChange={setCity}
                style={styles.picker}
                dropdownIconColor="#aaa"
              >
                <Picker.Item label="City *" value="" color="#aaa" />
                {cities.map((c) => (
                  <Picker.Item key={c} label={c} value={c} />
                ))}
              </Picker>
            </View>
            <View style={styles.pickerWrapper}>
              <Picker
                selectedValue={region}
                onValueChange={setRegion}
                style={styles.picker}
                dropdownIconColor="#aaa"
              >
                <Picker.Item label="Region *" value="" color="#aaa" />
                {regions.map((r) => (
                  <Picker.Item key={r} label={r} value={r} />
                ))}
              </Picker>
            </View>
            <View style={{ flexDirection: 'row', alignItems: 'center' }}>
              <TextInput
                style={[styles.input, { flex: 1 }]}
                placeholder="Choose Location Using Maps *"
                placeholderTextColor="#aaa"
                value={locationLoading ? 'Loading location...' : location}
                onChangeText={setLocation}
                editable={false}
              />
              <TouchableOpacity style={styles.mapButton} onPress={getCurrentLocationAndSet}>
                <Text style={styles.mapButtonText}>Use My Location</Text>
              </TouchableOpacity>
            </View>
          </View>
          <View style={styles.buttonRow}>
            <TouchableOpacity style={styles.backButton} onPress={() => setStep(1)}>
              <Text style={styles.nextButtonText}>Back</Text>
            </TouchableOpacity>
            <TouchableOpacity style={styles.nextButton} onPress={() => setStep(3)}>
              <Text style={styles.nextButtonText}>Next</Text>
            </TouchableOpacity>
          </View>
        </>
      ) : step === 3 ? (
        <>
          {/* Account Credentials Form */}
          <Text style={styles.formTitle}>Account Credentials</Text>
          <View style={styles.formCard}>
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
          </View>
          <View style={styles.buttonRow}>
            <TouchableOpacity style={styles.backButton} onPress={() => setStep(2)}>
              <Text style={styles.nextButtonText}>Back</Text>
            </TouchableOpacity>
            <TouchableOpacity style={styles.nextButton} onPress={() => setStep(4)}>
              <Text style={styles.nextButtonText}>Next</Text>
            </TouchableOpacity>
          </View>
        </>
      ) : (
        <>
          {/* Verification Documents Step */}
          <Text style={styles.formTitle}>Verification Documents</Text>
          <View style={styles.formCard}>
            <View style={styles.fileRow}>
              <Text style={styles.fileLabel}>Business Permit / License</Text>
              <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                <TouchableOpacity
                  style={[styles.fileButton, permitFile ? { opacity: 0.5 } : {}]}
                  onPress={() => pickFile(setPermitFile)}
                  disabled={!!permitFile}
                  pointerEvents={permitFile ? 'none' : 'auto'}
                >
                  <Text style={styles.fileButtonText}>{permitFile ? 'File Chosen' : 'Choose File'}</Text>
                </TouchableOpacity>
                <Text style={styles.fileProgress}>{permitFile ? '1/1' : '0/1'}</Text>
                {permitFile && (
                  <TouchableOpacity style={styles.removeFileButton} onPress={() => setPermitFile(null)}>
                    <Text style={styles.removeFileButtonText}>Remove</Text>
                  </TouchableOpacity>
                )}
              </View>
            </View>
            <View style={styles.fileRow}>
              <Text style={styles.fileLabel}>Valid Government ID</Text>
              <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                <TouchableOpacity
                  style={[styles.fileButton, idFile ? { opacity: 0.5 } : {}]}
                  onPress={() => pickFile(setIdFile)}
                  disabled={!!idFile}
                  pointerEvents={idFile ? 'none' : 'auto'}
                >
                  <Text style={styles.fileButtonText}>{idFile ? 'File Chosen' : 'Choose File'}</Text>
                </TouchableOpacity>
                <Text style={styles.fileProgress}>{idFile ? '1/1' : '0/1'}</Text>
                {idFile && (
                  <TouchableOpacity style={styles.removeFileButton} onPress={() => setIdFile(null)}>
                    <Text style={styles.removeFileButtonText}>Remove</Text>
                  </TouchableOpacity>
                )}
              </View>
            </View>
            <View style={styles.fileRow}>
              <Text style={styles.fileLabel}>Proof of Address</Text>
              <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                <TouchableOpacity
                  style={[styles.fileButton, addressFile ? { opacity: 0.5 } : {}]}
                  onPress={() => pickFile(setAddressFile)}
                  disabled={!!addressFile}
                  pointerEvents={addressFile ? 'none' : 'auto'}
                >
                  <Text style={styles.fileButtonText}>{addressFile ? 'File Chosen' : 'Choose File'}</Text>
                </TouchableOpacity>
                <Text style={styles.fileProgress}>{addressFile ? '1/1' : '0/1'}</Text>
                {addressFile && (
                  <TouchableOpacity style={styles.removeFileButton} onPress={() => setAddressFile(null)}>
                    <Text style={styles.removeFileButtonText}>Remove</Text>
                  </TouchableOpacity>
                )}
              </View>
            </View>
          </View>
          <View style={styles.checkboxRow}>
            <CheckBox
              isChecked={agreeTerms}
              onClick={() => setAgreeTerms(!agreeTerms)}
              checkBoxColor="#3FE0E8"
            />
            <Text style={styles.checkboxLabel}>Terms & Conditions Agreement</Text>
          </View>
          <View style={styles.checkboxRow}>
            <CheckBox
              isChecked={agreePrivacy}
              onClick={() => setAgreePrivacy(!agreePrivacy)}
              checkBoxColor="#3FE0E8"
            />
            <Text style={styles.checkboxLabel}>Privacy Policy Agreement</Text>
          </View>
          <View style={styles.buttonRow}>
            <TouchableOpacity style={styles.backButton} onPress={() => setStep(3)}>
              <Text style={styles.nextButtonText}>Back</Text>
            </TouchableOpacity>
            <TouchableOpacity style={styles.nextButton} onPress={handleSubmit}>
              <Text style={styles.nextButtonText}>Finish</Text>
            </TouchableOpacity>
          </View>
        </>
      )}
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
    alignItems: 'center',
    paddingTop: 40,
    paddingHorizontal: 16,
  },
  logoContainer: {
    alignItems: 'flex-start',
    width: '100%',
    marginBottom: 18,
  },
  logo: {
    width: 110,
    height: 50,
  },
  progressLabel: {
    fontSize: 16,
    color: '#222',
    marginBottom: 6,
    alignSelf: 'flex-start',
    marginLeft: 2,
  },
  progressBarBg: {
    width: '100%',
    maxWidth: CARD_MAX_WIDTH,
    height: 10,
    backgroundColor: '#ccc',
    borderRadius: 8,
    marginBottom: 28,
    overflow: 'hidden',
  },
  progressBarFill: {
    height: '100%',
    backgroundColor: '#3FE0E8',
    borderRadius: 8,
  },
  formTitle: {
    fontSize: 22,
    fontWeight: 'bold',
    color: '#222',
    marginBottom: 18,
    alignSelf: 'flex-start',
  },
  formCard: {
    width: '100%',
    maxWidth: CARD_MAX_WIDTH,
    backgroundColor: '#fff',
    borderRadius: 14,
    padding: 10,
    marginBottom: 30,
    elevation: 2,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.06,
    shadowRadius: 4,
  },
  input: {
    width: '100%',
    backgroundColor: '#F7F7F7',
    borderRadius: 8,
    paddingVertical: 12,
    paddingHorizontal: 14,
    fontSize: 16,
    marginBottom: 14,
    color: '#222',
    borderBottomWidth: 1,
    borderBottomColor: '#e0e0e0',
    elevation: 1,
  },
  pickerWrapper: {
    width: '100%',
    backgroundColor: '#F7F7F7',
    borderRadius: 8,
    marginBottom: 14,
    borderBottomWidth: 1,
    borderBottomColor: '#e0e0e0',
    overflow: 'hidden',
  },
  picker: {
    width: '100%',
    color: '#222',
    backgroundColor: 'transparent',
  },
  buttonRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    width: '100%',
    maxWidth: CARD_MAX_WIDTH,
    marginTop: 10,
  },
  nextButton: {
    backgroundColor: '#3FE0E8',
    borderRadius: 8,
    paddingVertical: 13,
    paddingHorizontal: 60,
    alignSelf: 'center',
    elevation: 2,
    marginTop: 10,
  },
  backButton: {
    backgroundColor: '#3FE0E8',
    borderRadius: 8,
    paddingVertical: 13,
    paddingHorizontal: 60,
    alignSelf: 'center',
    elevation: 2,
    marginTop: 10,
  },
  nextButtonText: {
    color: '#fff',
    fontSize: 17,
    fontWeight: 'bold',
    letterSpacing: 0.2,
    textAlign: 'center',
  },
  mapButton: {
    backgroundColor: '#3FE0E8',
    borderRadius: 8,
    paddingVertical: 10,
    paddingHorizontal: 14,
    marginLeft: 8,
    elevation: 2,
  },
  mapButtonText: {
    color: '#fff',
    fontWeight: 'bold',
    fontSize: 13,
  },
  fileRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 10,
  },
  fileLabel: {
    fontSize: 16,
    color: '#222',
  },
  fileButton: {
    backgroundColor: '#3FE0E8',
    borderRadius: 8,
    paddingVertical: 10,
    paddingHorizontal: 14,
  },
  fileButtonText: {
    color: '#fff',
    fontWeight: 'bold',
    fontSize: 13,
  },
  fileProgress: {
    fontSize: 14,
    color: '#3578C9',
    marginLeft: 8,
    fontWeight: 'bold',
  },
  checkboxRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 10,
  },
  checkboxLabel: {
    fontSize: 16,
    color: '#222',
    marginLeft: 10,
  },
  removeFileButton: {
    backgroundColor: '#ff3b30',
    borderRadius: 8,
    paddingVertical: 6,
    paddingHorizontal: 10,
    marginLeft: 8,
  },
  removeFileButtonText: {
    color: '#fff',
    fontWeight: 'bold',
    fontSize: 12,
  },
});
