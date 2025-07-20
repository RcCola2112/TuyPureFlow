import React, { useState, useEffect } from 'react';
import { View, Text, TextInput, TouchableOpacity, StyleSheet, ScrollView, useWindowDimensions, Alert, ActivityIndicator } from 'react-native';
import { createDrawerNavigator, DrawerContentScrollView } from '@react-navigation/drawer';

const API_BASE_URL = 'http://192.168.1.3/pureflowBackend'; // Change to your backend IP

function ProfileForm({ navigation, route }) {
  const { width } = useWindowDimensions();
  const isMobile = width < 600;
  const consumer_id = route?.params?.consumer_id;
  const [loading, setLoading] = useState(true);
  const [userName, setUserName] = useState('');
  const [fullName, setFullName] = useState('');
  const [contactNumber, setContactNumber] = useState('');
  const [currentPassword, setCurrentPassword] = useState('');
  const [newPassword, setNewPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  const [error, setError] = useState('');

  useEffect(() => {
    if (!consumer_id) {
      setError('No user found. Please log in again.');
      setLoading(false);
      return;
    }
    setLoading(true);
    fetch(`${API_BASE_URL}/get_consumer_profile.php?consumer_id=${consumer_id}`)
      .then(res => res.json())
      .then(data => {
        if (data.success && data.user) {
          setUserName(data.user.name || '');
          setFullName(data.user.name || '');
          setContactNumber(data.user.phone || '');
        } else {
          setError('Failed to fetch user info.');
        }
      })
      .catch(() => setError('Error: Failed to fetch user info.'))
      .finally(() => setLoading(false));
  }, [consumer_id]);

  const handleConfirmChanges = async () => {
    if (!consumer_id) return;
    setLoading(true);
    setError('');
    try {
      const response = await fetch(`${API_BASE_URL}/update_consumer_profile.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          consumer_id,
          name: userName,
          full_name: fullName,
          phone: contactNumber,
          password: newPassword ? newPassword : undefined,
        }),
      });
      const result = await response.json();
      if (result.success) {
        Alert.alert('Success', 'Profile updated successfully!');
        setCurrentPassword('');
        setNewPassword('');
        setConfirmPassword('');
      } else {
        setError(result.message || 'Update failed.');
      }
    } catch (e) {
      setError('Network error.');
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}><ActivityIndicator size="large" color="#3578C9" /></View>;
  }
  if (error) {
    return <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}><Text style={{ color: 'red', fontSize: 16 }}>{error}</Text></View>;
  }

  return (
    <View style={{flex: 1, backgroundColor: '#f7fafd'}}>
      <View style={styles.navbar}>
        <TouchableOpacity style={styles.navbarMenuBtn} onPress={() => navigation.openDrawer()}>
          <Text style={styles.navbarMenuIcon}>☰</Text>
        </TouchableOpacity>
        <Text style={styles.navbarTitle}>Profile</Text>
      </View>
      <ScrollView contentContainerStyle={[styles.formContainer, isMobile && styles.formContainerMobile]}>
        <View style={styles.sectionCard}>
          <Text style={styles.sectionHeader}>Account Information</Text>
          <Text style={styles.label}>User Name</Text>
          <TextInput style={styles.input} value={userName} onChangeText={setUserName} />
          <Text style={styles.label}>Full Name</Text>
          <TextInput style={styles.input} value={fullName} onChangeText={setFullName} />
          <Text style={styles.label}>Contact Number</Text>
          <TextInput style={styles.input} value={contactNumber} onChangeText={setContactNumber} />
        </View>
        <View style={styles.sectionCard}>
          <Text style={styles.sectionHeader}>Change Password</Text>
          <Text style={styles.label}>Current Password</Text>
          <TextInput style={styles.input} value={currentPassword} onChangeText={setCurrentPassword} secureTextEntry />
          <Text style={styles.label}>New Password</Text>
          <TextInput style={styles.input} value={newPassword} onChangeText={setNewPassword} secureTextEntry />
          <Text style={styles.label}>Confirm Password</Text>
          <TextInput style={styles.input} value={confirmPassword} onChangeText={setConfirmPassword} secureTextEntry />
        </View>
        <TouchableOpacity style={styles.confirmBtn} onPress={handleConfirmChanges}>
          <Text style={styles.confirmBtnText}>Confirm Changes</Text>
        </TouchableOpacity>
      </ScrollView>
    </View>
  );
}

function CustomDrawerContent(props) {
  const consumer_id = props?.state?.routes[0]?.params?.consumer_id || props?.route?.params?.consumer_id;
  const [userName, setUserName] = useState('');
  useEffect(() => {
    if (!consumer_id) return;
    fetch(`${API_BASE_URL}/get_consumer_profile.php?consumer_id=${consumer_id}`)
      .then(res => res.json())
      .then(data => {
        if (data.success && data.user) {
          setUserName(data.user.name || '');
        }
      });
  }, [consumer_id]);
  return (
    <DrawerContentScrollView {...props} contentContainerStyle={styles.drawerContainer}>
      <View style={styles.drawerHeader}>
        <View style={styles.avatarCircle} />
        <TouchableOpacity onPress={() => alert('Change Photo functionality not implemented yet.')}>
          <Text style={styles.changePhotoDrawerText}>Change Photo</Text>
        </TouchableOpacity>
        <Text style={styles.drawerUserName}>{userName}</Text>
      </View>
      <TouchableOpacity style={styles.drawerLinkBtn}><Text style={styles.drawerLink}>My Account</Text></TouchableOpacity>
      <TouchableOpacity style={styles.drawerLinkBtn}><Text style={styles.drawerLink}>My Purchase</Text></TouchableOpacity>
      <TouchableOpacity style={styles.drawerLinkBtn}><Text style={styles.drawerLink}>Notification</Text></TouchableOpacity>
      <TouchableOpacity style={styles.drawerLogoutBtn} onPress={() => alert('Logout')}>
        <Text style={styles.drawerLogoutLabel}>Logout</Text>
      </TouchableOpacity>
    </DrawerContentScrollView>
  );
}

const Drawer = createDrawerNavigator();

export default function ProfileScreen({ route }) {
  // Pass consumer_id to ProfileForm and Drawer
  const consumer_id = route?.params?.user?.consumer_id || route?.params?.consumer_id;
  return (
    <Drawer.Navigator
      drawerContent={props => <CustomDrawerContent {...props} route={{ params: { consumer_id } }} />}
      screenOptions={{
        headerShown: false,
        drawerType: 'slide',
        overlayColor: 'rgba(0,0,0,0.05)',
        drawerStyle: { backgroundColor: '#fff', width: 270 },
      }}
    >
      <Drawer.Screen name="ProfileForm">
        {props => <ProfileForm {...props} route={{ params: { consumer_id } }} />}
      </Drawer.Screen>
    </Drawer.Navigator>
  );
}

const styles = StyleSheet.create({
  menuBtn: {
    position: 'absolute',
    top: 18,
    left: 18,
    zIndex: 10,
    backgroundColor: '#f7fafd',
    borderRadius: 24,
    width: 44,
    height: 44,
    justifyContent: 'center',
    alignItems: 'center',
    borderWidth: 1,
    borderColor: '#e0e0e0',
    elevation: 3,
    shadowColor: '#3578C9',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.10,
    shadowRadius: 4,
  },
  menuIcon: {
    fontSize: 26,
    color: '#3578C9',
    fontWeight: 'bold',
    textAlign: 'center',
    letterSpacing: 2,
  },
  drawerContainer: {
    flex: 1,
    paddingTop: 32,
    backgroundColor: '#fff',
  },
  drawerHeader: {
    alignItems: 'center',
    marginBottom: 18,
  },
  avatarCircle: {
    width: 60,
    height: 60,
    borderRadius: 30,
    backgroundColor: '#ccc',
    marginBottom: 8,
    alignSelf: 'center',
  },
  drawerUserName: {
    fontSize: 16,
    color: '#3578C9',
    fontWeight: 'bold',
    marginBottom: 18,
    alignSelf: 'center',
  },
  drawerLinkBtn: {
    backgroundColor: '#f7f7f7',
    borderRadius: 8,
    marginBottom: 12,
    paddingVertical: 12,
    paddingHorizontal: 18,
    alignSelf: 'stretch',
    marginHorizontal: 8,
  },
  drawerLink: {
    fontSize: 16,
    color: '#3578C9',
    fontWeight: '500',
  },
  drawerLogoutBtn: {
    borderWidth: 1,
    borderColor: '#ff3b30',
    borderRadius: 8,
    marginTop: 24,
    marginHorizontal: 8,
    paddingVertical: 12,
    paddingHorizontal: 18,
    alignSelf: 'stretch',
    backgroundColor: '#fff',
  },
  drawerLogoutLabel: {
    color: '#ff3b30',
    fontWeight: 'bold',
    fontSize: 16,
    textAlign: 'center',
  },
  formContainer: {
    flexGrow: 1,
    backgroundColor: '#f7fafd',
    borderRadius: 12,
    padding: 20,
    minWidth: 260,
    maxWidth: 500,
    alignSelf: 'center',
    paddingTop: 48,
  },
  formContainerMobile: {
    minWidth: undefined,
    maxWidth: '100%',
    alignSelf: 'stretch',
    padding: 10,
    borderRadius: 10,
    paddingTop: 48,
  },
  sectionCard: {
    backgroundColor: '#fff',
    borderRadius: 10,
    padding: 16,
    marginBottom: 18,
    elevation: 1,
  },
  sectionHeader: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#3578C9',
    marginBottom: 10,
  },
  label: {
    fontSize: 15,
    color: '#888',
    marginTop: 8,
    marginBottom: 2,
  },
  input: {
    backgroundColor: '#f7f7f7',
    borderRadius: 8,
    paddingVertical: 10,
    paddingHorizontal: 14,
    fontSize: 16,
    marginBottom: 10,
    color: '#222',
    borderWidth: 1,
    borderColor: '#e0e0e0',
  },
  photoSection: {
    alignItems: 'center',
    justifyContent: 'center',
    paddingTop: 10,
    paddingBottom: 10,
  },
  photoRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: 8,
    marginBottom: 8,
  },
  avatarCircleLarge: {
    width: 60,
    height: 60,
    borderRadius: 30,
    backgroundColor: '#ccc',
    marginRight: 18,
  },
  changePhotoText: {
    color: '#3FE0E8',
    fontSize: 18,
    textDecorationLine: 'underline',
  },
  confirmBtn: {
    backgroundColor: '#3FE0E8',
    borderRadius: 8,
    paddingVertical: 13,
    paddingHorizontal: 30,
    alignSelf: 'center',
    elevation: 2,
    marginTop: 10,
    marginBottom: 20,
  },
  confirmBtnText: {
    color: '#fff',
    fontSize: 17,
    fontWeight: 'bold',
    letterSpacing: 0.2,
    textAlign: 'center',
  },
  navbar: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#3578C9',
    height: 54,
    paddingHorizontal: 10,
    elevation: 4,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.08,
    shadowRadius: 2,
  },
  navbarMenuBtn: {
    width: 44,
    height: 44,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 8,
  },
  navbarMenuIcon: {
    fontSize: 28,
    color: '#fff',
    fontWeight: 'bold',
    textAlign: 'center',
    letterSpacing: 2,
  },
  navbarTitle: {
    color: '#fff',
    fontSize: 20,
    fontWeight: 'bold',
    textAlign: 'center',
    flex: 1,
    marginRight: 44, // to balance the menu button
  },
  changePhotoDrawerText: {
    color: '#3FE0E8',
    fontSize: 16,
    textDecorationLine: 'underline',
    marginBottom: 8,
    alignSelf: 'center',
  },
}); 