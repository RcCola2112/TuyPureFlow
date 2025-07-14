import React, { useState } from 'react';
import { View, Text, Image, StyleSheet, ScrollView, TouchableOpacity, TextInput, useWindowDimensions, Platform } from 'react-native';
import { Ionicons, FontAwesome, FontAwesome5, Entypo } from '@expo/vector-icons';
import { SafeAreaView } from 'react-native-safe-area-context';

export default function ConsumerNavBar({ navigation, search, setSearch }) {
  const { width } = useWindowDimensions();
  const isTablet = width > 600;
  const [language, setLanguage] = useState('English');

  return (
    <SafeAreaView style={styles.safeArea} edges={['top', 'left', 'right']}>
      <View style={styles.bg}>
        {/* Top Navbar */}
        <View style={[styles.responsiveContainer, isTablet && styles.responsiveContainerTablet]}>
          <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={[styles.topNavBar, isTablet && styles.topNavBarTablet, isTablet && styles.topNavBarTabletPad]}>
            <TouchableOpacity style={styles.navBtn} onPress={() => navigation.navigate('Home')}><Text style={styles.navBtnActive}>Home</Text></TouchableOpacity>
            <TouchableOpacity style={styles.navBtn} onPress={() => navigation.navigate('Services')}>
              <Text style={styles.navBtnText}>Services</Text>
              <Entypo name="chevron-down" size={16} color="#888" style={{ marginLeft: 2 }} />
            </TouchableOpacity>
            <TouchableOpacity style={styles.navBtn} onPress={() => navigation.navigate('AboutUs')}><Text style={styles.navBtnText}>About Us</Text></TouchableOpacity>
            <TouchableOpacity style={styles.navBtn} onPress={() => navigation.navigate('ContactUs')}><Text style={styles.navBtnText}>Contact Us</Text></TouchableOpacity>
            <TouchableOpacity style={styles.navBtn}>
              <FontAwesome name="globe" size={16} color="#3FE0E8" />
              <Text style={[styles.navBtnText, { marginLeft: 3 }]}>{language}</Text>
              <Entypo name="chevron-down" size={16} color="#888" style={{ marginLeft: 2 }} />
            </TouchableOpacity>
            <TouchableOpacity style={styles.navBtn}>
              <Ionicons name="notifications" size={18} color="#3FE0E8" />
              <View style={styles.notifDot} />
              <Text style={[styles.navBtnText, { color: '#3FE0E8', marginLeft: 3 }]}>Notification</Text>
            </TouchableOpacity>
            <View style={styles.userWrap}>
              <View style={styles.userCircle} />
              <Text style={styles.userName}>User Name</Text>
            </View>
          </ScrollView>
        </View>
        {/* Logo and Search Bar */}
        <View style={[styles.responsiveContainer, isTablet && styles.responsiveContainerTablet]}>
          <View style={[styles.header, isTablet && styles.headerTablet, isTablet && styles.headerTabletPad]}>
            <Image source={require('../assets/PureLogo.png')} style={styles.logo} resizeMode="contain" />
            <TextInput
              style={styles.searchBar}
              placeholder="Search"
              placeholderTextColor="#aaa"
              value={search}
              onChangeText={setSearch}
            />
            <TouchableOpacity style={styles.iconBtn}>
              <FontAwesome name="search" size={18} color="#3FE0E8" />
            </TouchableOpacity>
            <TouchableOpacity style={styles.iconBtn} onPress={() => navigation.navigate('CartCheckout')}>
              <FontAwesome5 name="shopping-cart" size={18} color="#3FE0E8" />
              <Text style={styles.iconLabel}>Cart</Text>
            </TouchableOpacity>
            <TouchableOpacity style={styles.iconBtn} onPress={() => navigation.navigate('FollowingShop')}>
              <FontAwesome5 name="user-friends" size={18} color="#3FE0E8" />
              <Text style={styles.iconLabel}>Following Shop</Text>
            </TouchableOpacity>
          </View>
        </View>
      </View>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safeArea: {
    backgroundColor: '#fff',
  },
  bg: {
    backgroundColor: '#fff',
  },
  responsiveContainer: {
    width: '100%',
    alignSelf: 'stretch',
  },
  responsiveContainerTablet: {
    maxWidth: 900,
    alignSelf: 'center',
  },
  topNavBar: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 8,
    paddingTop: 8,
    paddingBottom: 2,
    backgroundColor: '#fff',
    borderBottomWidth: 1,
    borderBottomColor: '#eee',
  },
  topNavBarTablet: {
    justifyContent: 'center',
    paddingTop: 2,
    paddingBottom: 2,
  },
  topNavBarTabletPad: {
    paddingTop: 24,
  },
  navBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    marginRight: 16,
    paddingVertical: 2,
  },
  navBtnText: {
    color: '#888',
    fontSize: 16,
  },
  navBtnActive: {
    color: '#3FE0E8',
    fontSize: 16,
    fontWeight: 'bold',
    textDecorationLine: 'underline',
  },
  notifDot: {
    width: 8,
    height: 8,
    borderRadius: 4,
    backgroundColor: '#ff3b30',
    position: 'absolute',
    top: 0,
    right: -6,
    borderWidth: 1,
    borderColor: '#fff',
  },
  userWrap: {
    flexDirection: 'row',
    alignItems: 'center',
    marginLeft: 10,
  },
  userCircle: {
    width: 22,
    height: 22,
    borderRadius: 11,
    backgroundColor: '#ccc',
    marginRight: 5,
  },
  userName: {
    color: '#888',
    fontSize: 16,
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 16,
    paddingTop: 12,
    paddingBottom: 8,
    backgroundColor: '#fff',
  },
  headerTablet: {
    justifyContent: 'center',
    paddingTop: 6,
    paddingBottom: 6,
  },
  headerTabletPad: {
    paddingTop: 16,
  },
  logo: {
    width: 90,
    height: 40,
    marginRight: 10,
  },
  searchBar: {
    flex: 1,
    backgroundColor: '#f4f7fb',
    borderRadius: 20,
    paddingVertical: 10,
    paddingHorizontal: 18,
    fontSize: 16,
    color: '#222',
    borderWidth: 1,
    borderColor: '#e0e0e0',
  },
  iconBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    marginLeft: 10,
  },
  iconLabel: {
    color: '#3FE0E8',
    fontSize: 15,
    marginLeft: 3,
  },
}); 