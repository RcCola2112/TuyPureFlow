import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity, Image } from 'react-native';
import { DrawerContentScrollView, DrawerItemList } from '@react-navigation/drawer';
import { LinearGradient } from 'expo-linear-gradient';

export default function CustomDrawerContent(props) {
  const { distributor, shopName } = props;
  return (
    <View style={styles.container}>
      <LinearGradient colors={["#3FE0E8", "#3578C9"]} style={styles.header}>
        <Image source={require('../assets/PureLogo.png')} style={styles.logo} resizeMode="contain" />
        <Text style={styles.welcomeText}>Welcome Back!</Text>
        <Text style={styles.userText}>{distributor?.name || 'Owner'} of {shopName || '...'}</Text>
      </LinearGradient>
      
      <DrawerContentScrollView {...props} style={styles.drawerContent}>
        <View style={styles.drawerItems}>
          <DrawerItemList {...props} />
        </View>
      </DrawerContentScrollView>
      
      <View style={styles.bottomSection}>
        <TouchableOpacity style={styles.logoutButton} onPress={() => {
          // Handle logout
          props.navigation.closeDrawer();
          // Add your logout logic here
        }}>
          <Text style={styles.logoutIcon}>🚪</Text>
          <Text style={styles.logoutText}>Logout</Text>
        </TouchableOpacity>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f4f7fb',
  },
  header: {
    padding: 20,
    paddingTop: 40,
    paddingBottom: 30,
    alignItems: 'center',
  },
  logo: {
    width: 80,
    height: 45,
    marginBottom: 15,
  },
  welcomeText: {
    color: '#fff',
    fontSize: 18,
    fontWeight: '600',
    marginBottom: 5,
  },
  userText: {
    color: '#fff',
    fontSize: 16,
    opacity: 0.9,
  },
  drawerContent: {
    flex: 1,
  },
  drawerItems: {
    paddingTop: 10,
  },
  bottomSection: {
    padding: 20,
    borderTopWidth: 1,
    borderTopColor: '#e0eaf0',
  },
  logoutButton: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 15,
    paddingHorizontal: 20,
    backgroundColor: '#fff',
    borderRadius: 12,
    elevation: 2,
    shadowColor: '#3578C9',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
  },
  logoutIcon: {
    fontSize: 24,
    marginRight: 15,
  },
  logoutText: {
    fontSize: 16,
    fontWeight: '600',
    color: '#ff3b30',
  },
}); 