import React from 'react';
import { View, Text, Image, StyleSheet, ScrollView, TouchableOpacity, Dimensions } from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { NavigationContainer } from '@react-navigation/native';
import { createStackNavigator } from '@react-navigation/stack';
import Login from './DistributorLogin/Login';
import ConsumerLogin from './Consumer/ConsumerLogin';
import ConsumerSignUp from './Consumer/ConsumerSignUp';
import ConsumerDB from './Consumer/ConsumerDB';
import DistributorDrawerNavigator from './DistributorLogin/DistributorDrawerNavigator';
import AboutUsScreen from './Consumer/AboutUsScreen';
import ContactUsScreen from './Consumer/ContactUsScreen';
import ServicesScreen from './Consumer/ServicesScreen';
import FollowingShopScreen from './Consumer/FollowingShopScreen';
import ShopDetailScreen from './Consumer/ShopDetailScreen';
import CartCheckoutScreen from './Consumer/CartCheckoutScreen';

const NAV_ITEMS = [
  { label: 'Home', onPress: () => alert('Home clicked!') },
  { label: 'Services', onPress: () => alert('Services clicked!') },
  { label: 'About Us', onPress: () => alert('About Us clicked!') },
  { label: 'Contact Us', onPress: () => alert('Contact Us clicked!') },
  { label: 'Sign in', onPress: () => alert('Sign in clicked!') },
];

const Stack = createStackNavigator();

function HomeScreen({ navigation }) {
  const { width } = Dimensions.get('window');
  const CARD_MAX_WIDTH = width > 500 ? 500 : width - 32;
  return (
    <View style={styles.appBg}>
      <ScrollView contentContainerStyle={styles.container} showsVerticalScrollIndicator={false}>
        <NavBar navigation={navigation} />
        <View style={styles.logoGradientContainer}>
          <LinearGradient
            colors={["#fff", "#fff0"]}
            style={styles.logoGradient}
            start={{ x: 0.5, y: 0 }}
            end={{ x: 0.5, y: 1 }}
          >
            <View style={styles.logoContainer}>
              <Image source={require('./assets/PureLogo.png')} style={styles.logo} resizeMode="contain" />
            </View>
          </LinearGradient>
        </View>
        <View style={[styles.sectionBlue, { maxWidth: CARD_MAX_WIDTH }]}> 
          <Text style={styles.title}>What is PureFlow?</Text>
          <Text style={styles.description}>
            Tuy PureFlow is an innovative digital platform designed for efficient and hassle-free purified water distribution. Whether you're a consumer looking for a seamless ordering experience or a distributor managing deliveries, PureFlow optimizes every step. With AI-powered analytics, real-time tracking, and smart inventory management, we ensure clean water reaches you when you need it, without the wait.
          </Text>
        </View>
        <Text style={styles.sectionTitle}>Why use PureFlow?</Text>
        <View style={[styles.featuresContainer, { maxWidth: CARD_MAX_WIDTH }]}> 
          <FeatureItem icon={"\uD83D\uDCF1"} title="Convenience at Your Fingertips" desc="Order purified water anytime, anywhere with just a few taps—no more hassle of calling or visiting stores." />
          <FeatureItem icon={"\uD83D\uDE97"} title="Fast & Reliable Delivery" desc="We connect you with trusted water distributors in your area for quick and efficient delivery." />
          <FeatureItem icon={"\uD83D\uDCBC"} title="Find the Best Shops Near You" desc="Compare top-rated water shops based on reviews, delivery time, and pricing." />
          <FeatureItem icon={"\u23F1\uFE0F"} title="Real-Time Order Tracking" desc="Stay updated on your order status and track your delivery in real-time." />
          <FeatureItem icon={"\uD83D\uDD0D"} title="No Hidden Fees" desc="Enjoy transparent pricing with no extra charges—pay only for what you order." />
          <FeatureItem icon={"\uD83D\uDC4D"} title="Sustainable & Trusted" desc="We partner with verified distributors to ensure you get quality drinking water while supporting local businesses." />
        </View>
        <View style={{ height: 30 }} />
        {/* Distributor Section */}
        <View style={[styles.distributorSection, { maxWidth: CARD_MAX_WIDTH }]}> 
          <Text style={styles.distributorTitle}>Join us as a Distributor</Text>
          <Text style={styles.distributorSubtitle}>Expand Your Business with PureFlow!</Text>
          <Text style={styles.distributorDesc}>
            Become a part of Tuy PureFlow and grow your water distribution business with our smart platform. Get access to a large customer base, automated order management, and real-time analytics.
          </Text>
          {/* Why Partner with Us */}
          <Text style={styles.distributorSectionTitle}>Why Partner with Us?</Text>
          <View style={styles.distributorFeaturesRow}>
            <DistributorFeature icon={"\u270C\uFE0F"} title="Increase Your Sales" desc="Reach more customers looking for purified water." />
            <DistributorFeature icon={"\uD83D\uDCBC"} title="Efficient Order Management" desc="Process and track orders with ease." />
          </View>
          <View style={styles.distributorFeaturesRow}>
            <DistributorFeature icon={"\u2699\uFE0F"} title="Optimized Delivery Routes" desc="Reduce costs with AI-powered delivery optimization." />
            <DistributorFeature icon={"\uD83D\uDCB0"} title="Real-Time Insights" desc="Monitor sales, inventory, and customer trends." />
          </View>
          {/* How It Works */}
          <Text style={styles.distributorSectionTitle}>How It Works?</Text>
          <View style={styles.distributorFeaturesRow}>
            <DistributorFeature icon={"\u270D\uFE0F"} title="Sign Up" desc="Register your business and submit verification documents." />
            <DistributorFeature icon={"\u2705"} title="Get Approved" desc="Process and track orders with ease." />
          </View>
          <View style={styles.distributorFeaturesRow}>
            <DistributorFeature icon={"\uD83D\uDCB3"} title="Start Selling" desc="List your products, set delivery options, and receive orders." />
          </View>
          <View style={{ flexDirection: 'row', justifyContent: 'center', marginTop: 18 }}>
          <TouchableOpacity style={styles.continueButton} onPress={() => navigation.navigate('Login')}>
              <Text style={styles.continueButtonText}>Sign up</Text>
            </TouchableOpacity>
            <TouchableOpacity style={[styles.continueButton, { marginLeft: 12, backgroundColor: '#3578C9' }]} onPress={() => navigation.navigate('DistributorLoginScreen')}>
              <Text style={styles.continueButtonText}>Login</Text>
          </TouchableOpacity>
          </View>
        </View>
      </ScrollView>
    </View>
  );
}

function NavBar({ navigation }) {
  return (
    <View style={styles.navBarWrapper}>
      <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={styles.navBar}>
        {NAV_ITEMS.slice(0, 4).map((item, idx) => (
          <TouchableOpacity key={item.label} onPress={item.onPress} style={styles.navItemTouchable}>
            <Text style={[styles.navItem, idx === 0 && styles.navItemActive]}>{item.label}</Text>
          </TouchableOpacity>
        ))}
        <View style={{ flex: 1 }} />
        <TouchableOpacity onPress={() => navigation.navigate('ConsumerLogin')} style={styles.navSignInTouchable}>
          <Text style={styles.navSignIn}>{NAV_ITEMS[4].label}</Text>
        </TouchableOpacity>
      </ScrollView>
    </View>
  );
}

export default function App() {
  return (
    <NavigationContainer>
      <Stack.Navigator screenOptions={{ headerShown: false }}>
        <Stack.Screen name="Home" component={HomeScreen} />
        <Stack.Screen name="Login" component={Login} />
        <Stack.Screen name="ConsumerLogin" component={ConsumerLogin} />
        <Stack.Screen name="ConsumerSignUp" component={ConsumerSignUp} />
        <Stack.Screen name="ConsumerDB" component={ConsumerDB} />
        <Stack.Screen name="DistributorDB" component={DistributorDrawerNavigator} />
        <Stack.Screen name="AboutUs" component={AboutUsScreen} />
        <Stack.Screen name="ContactUs" component={ContactUsScreen} />
        <Stack.Screen name="Services" component={ServicesScreen} />
        <Stack.Screen name="FollowingShop" component={FollowingShopScreen} />
        <Stack.Screen name="ShopDetail" component={ShopDetailScreen} />
        <Stack.Screen name="CartCheckout" component={CartCheckoutScreen} />
        <Stack.Screen name="DistributorLoginScreen" component={require('./DistributorLogin/DistributorLoginScreen').default} />
      </Stack.Navigator>
    </NavigationContainer>
  );
}

function FeatureItem({ icon, title, desc }) {
  return (
    <View style={styles.featureCard}>
      <View style={styles.featureIconWrap}>
        <Text style={styles.featureIcon}>{icon}</Text>
      </View>
      <View style={styles.featureTextContainer}>
        <Text style={styles.featureTitle}>{title}</Text>
        <Text style={styles.featureDesc}>{desc}</Text>
      </View>
    </View>
  );
}

function DistributorFeature({ icon, title, desc }) {
  return (
    <View style={styles.distributorFeatureCard}>
      <View style={styles.distributorFeatureIconWrap}>
        <Text style={styles.distributorFeatureIcon}>{icon}</Text>
      </View>
      <View style={styles.distributorFeatureTextContainer}>
        <Text style={styles.distributorFeatureTitle}>{title}</Text>
        <Text style={styles.distributorFeatureDesc}>{desc}</Text>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  appBg: {
    flex: 1,
    backgroundColor: '#EAF6FB',
  },
  navBarWrapper: {
    backgroundColor: '#fff',
    borderBottomWidth: 1,
    borderBottomColor: '#eee',
    elevation: 2,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.04,
    shadowRadius: 2,
    marginBottom: 18,
    width: '100%',
  },
  navBar: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 16,
    paddingHorizontal: 10,
    minWidth: '100%',
  },
  navItemTouchable: {
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 8,
    marginRight: 2,
  },
  navItem: {
    fontSize: 18,
    color: '#888',
    paddingHorizontal: 8,
    paddingVertical: 4,
  },
  navItemActive: {
    color: '#3578C9',
    fontWeight: 'bold',
    textDecorationLine: 'underline',
  },
  navSignInTouchable: {
    marginLeft: 16,
    backgroundColor: '#3578C9',
    borderRadius: 20,
    paddingHorizontal: 22,
    paddingVertical: 8,
    justifyContent: 'center',
    alignItems: 'center',
    elevation: 2,
  },
  navSignIn: {
    color: '#fff',
    fontWeight: 'bold',
    fontSize: 17,
    letterSpacing: 0.2,
  },
  container: {
    alignItems: 'center',
    paddingHorizontal: 16,
    paddingBottom: 24,
    backgroundColor: 'transparent',
  },
  logoGradientContainer: {
    width: '100%',
    alignItems: 'center',
    marginBottom: 18,
    marginTop: 8,
  },
  logoGradient: {
    width: '100%',
    alignItems: 'center',
    borderRadius: 20,
    paddingTop: 18,
    paddingBottom: 18,
  },
  logoContainer: {
    alignItems: 'center',
  },
  logo: {
    width: 140,
    height: 70,
  },
  sectionBlue: {
    backgroundColor: '#3578C9',
    borderRadius: 18,
    padding: 20,
    marginBottom: 32,
    width: '100%',
    alignSelf: 'center',
    elevation: 2,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.08,
    shadowRadius: 4,
  },
  title: {
    color: '#fff',
    fontSize: 22,
    fontWeight: 'bold',
    marginBottom: 10,
    letterSpacing: 0.2,
  },
  description: {
    color: '#fff',
    fontSize: 15,
    lineHeight: 22,
    letterSpacing: 0.1,
  },
  sectionTitle: {
    fontSize: 21,
    fontWeight: 'bold',
    marginBottom: 18,
    marginTop: 6,
    alignSelf: 'center',
    letterSpacing: 0.2,
  },
  featuresContainer: {
    width: '100%',
    alignSelf: 'center',
  },
  featureCard: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#fff',
    borderRadius: 14,
    paddingVertical: 16,
    paddingHorizontal: 14,
    marginBottom: 16,
    elevation: 2,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.07,
    shadowRadius: 4,
  },
  featureIconWrap: {
    width: 38,
    height: 38,
    borderRadius: 19,
    backgroundColor: '#F0F4FA',
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: 14,
  },
  featureIcon: {
    fontSize: 24,
  },
  featureTextContainer: {
    flex: 1,
  },
  featureTitle: {
    fontSize: 16.5,
    fontWeight: '700',
    marginBottom: 2,
    color: '#3578C9',
    letterSpacing: 0.1,
  },
  featureDesc: {
    fontSize: 14.5,
    color: '#333',
    letterSpacing: 0.05,
  },
  distributorSection: {
    backgroundColor: '#fff',
    borderRadius: 18,
    padding: 22,
    marginTop: 10,
    marginBottom: 30,
    width: '100%',
    alignSelf: 'center',
    elevation: 2,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.08,
    shadowRadius: 4,
  },
  distributorTitle: {
    fontSize: 22,
    fontWeight: 'bold',
    marginBottom: 8,
    color: '#222',
    letterSpacing: 0.2,
    textAlign: 'left',
  },
  distributorSubtitle: {
    fontSize: 18,
    fontWeight: '600',
    marginBottom: 6,
    color: '#3578C9',
    letterSpacing: 0.1,
    textAlign: 'left',
  },
  distributorDesc: {
    fontSize: 15,
    color: '#444',
    marginBottom: 18,
    lineHeight: 21,
    textAlign: 'left',
  },
  distributorSectionTitle: {
    fontSize: 17,
    fontWeight: 'bold',
    marginTop: 10,
    marginBottom: 10,
    color: '#222',
    letterSpacing: 0.1,
    textAlign: 'left',
  },
  distributorFeaturesRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 10,
  },
  distributorFeatureCard: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#F7FAFD',
    borderRadius: 12,
    paddingVertical: 12,
    paddingHorizontal: 10,
    marginHorizontal: 4,
    marginBottom: 4,
    elevation: 1,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.04,
    shadowRadius: 2,
  },
  distributorFeatureIconWrap: {
    width: 32,
    height: 32,
    borderRadius: 16,
    backgroundColor: '#E3F6FB',
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: 10,
  },
  distributorFeatureIcon: {
    fontSize: 20,
  },
  distributorFeatureTextContainer: {
    flex: 1,
  },
  distributorFeatureTitle: {
    fontSize: 15.5,
    fontWeight: '700',
    color: '#3578C9',
    marginBottom: 1,
  },
  distributorFeatureDesc: {
    fontSize: 13.5,
    color: '#555',
  },
  continueButton: {
    marginTop: 18,
    backgroundColor: '#3FE0E8',
    borderRadius: 22,
    alignSelf: 'center',
    paddingVertical: 12,
    paddingHorizontal: 38,
    elevation: 2,
  },
  continueButtonText: {
    color: '#fff',
    fontSize: 17,
    fontWeight: 'bold',
    letterSpacing: 0.2,
  },
});

