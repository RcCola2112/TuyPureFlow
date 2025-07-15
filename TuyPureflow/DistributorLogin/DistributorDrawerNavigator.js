import React from 'react';
import { createDrawerNavigator } from '@react-navigation/drawer';
import { TouchableOpacity, Text, StyleSheet } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useRoute } from '@react-navigation/native';
import { useState, useEffect } from 'react';

// Import screens
import DistributorDB from './DistributorDB';
import OrdersScreen from './OrdersScreen';
import InventoryScreen from './InventoryScreen';
import MessagesScreen from './MessagesScreen';
import AnalyticsScreen from './AnalyticsScreen';
import CustomerScreen from './CustomerScreen';
import SettingsScreen from './SettingsScreen';
import CustomDrawerContent from './DrawerNavigator';

const Drawer = createDrawerNavigator();

export default function DistributorDrawerNavigator({ route }) {
  const distributor = route?.params?.distributor;
  const [shopName, setShopName] = useState('');
  useEffect(() => {
    if (distributor?.distributor_id) {
      fetch(`http://192.168.1.20/pureflowBackend/get_shop_name.php?distributor_id=${distributor.distributor_id}`)
        .then(res => res.json())
        .then(data => {
          if (data.success && data.shop_name) setShopName(data.shop_name);
        });
    }
  }, [distributor]);
  return (
    <Drawer.Navigator
      drawerContent={(props) => <CustomDrawerContent {...props} distributor={distributor} shopName={shopName} />}
      screenOptions={{
        headerShown: false,
        drawerStyle: {
          backgroundColor: '#f4f7fb',
          width: 280,
        },
        drawerLabelStyle: {
          color: '#3578C9',
          fontSize: 16,
          fontWeight: '600',
          marginLeft: -10,
        },
        drawerActiveBackgroundColor: '#eaf6fa',
        drawerActiveTintColor: '#3578C9',
        drawerInactiveTintColor: '#666',
      }}
    >
      <Drawer.Screen
        name="Dashboard"
        component={DistributorDB}
        initialParams={{ distributor }}
        options={{
          drawerIcon: ({ color, size }) => (
            <Text style={[styles.drawerIcon, { color }]}>🏠</Text>
          ),
        }}
      />
      <Drawer.Screen
        name="Orders"
        component={OrdersScreen}
        options={{
          drawerIcon: ({ color, size }) => (
            <Text style={[styles.drawerIcon, { color }]}>🛒</Text>
          ),
        }}
      />
      <Drawer.Screen
        name="Inventory"
        component={InventoryScreen}
        initialParams={{ distributor }}
        options={{
          drawerIcon: ({ color, size }) => (
            <Text style={[styles.drawerIcon, { color }]}>📦</Text>
          ),
        }}
      />
      <Drawer.Screen
        name="Messages"
        component={MessagesScreen}
        options={{
          drawerIcon: ({ color, size }) => (
            <Text style={[styles.drawerIcon, { color }]}>✉️</Text>
          ),
        }}
      />
      <Drawer.Screen
        name="Analytics"
        component={AnalyticsScreen}
        options={{
          drawerIcon: ({ color, size }) => (
            <Text style={[styles.drawerIcon, { color }]}>📊</Text>
          ),
        }}
      />
      <Drawer.Screen
        name="Customers"
        component={CustomerScreen}
        options={{
          drawerIcon: ({ color, size }) => (
            <Text style={[styles.drawerIcon, { color }]}>👥</Text>
          ),
        }}
      />
      <Drawer.Screen
        name="Settings"
        component={SettingsScreen}
        options={{
          drawerIcon: ({ color, size }) => (
            <Text style={[styles.drawerIcon, { color }]}>⚙️</Text>
          ),
        }}
      />
    </Drawer.Navigator>
  );
}

const styles = StyleSheet.create({
  drawerIcon: {
    fontSize: 24,
    marginRight: 10,
  },
}); 