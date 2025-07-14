import React, { useState } from 'react';
import { View, Text, Image, StyleSheet, ScrollView, TouchableOpacity, Dimensions, RefreshControl, Platform } from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';

const { width } = Dimensions.get('window');
const CARD_WIDTH = width - 32;

const summaryData = [
  { label: 'Orders', value: '1,456', change: '+6.7%', sub: 'Since last week', icon: '🛒' },
  { label: 'Loans', value: '₱ 2,000', change: '+3.1%', sub: 'Since last week', icon: '👥' },
  { label: 'Estimated Sale', value: '₱ 23,456', change: '+1.1%', sub: 'Since last month', icon: '📈' },
  { label: 'Revenue Today', value: '₱ 838', change: '+1.1%', sub: 'Since yesterday', icon: '📊' },
];

const infoCards = [
  { label: 'On-Going Delivery', value: 2, icon: '🛒' },
  { label: 'Available Rider', value: 0, icon: '🧑‍🦱' },
  { label: 'Customer Complain', value: 0, icon: '👥' },
  { label: 'Damaged Container', value: 4, icon: '📦' },
];

export default function DistributorDB({ navigation }) {
  const [refreshing, setRefreshing] = useState(false);
  const onRefresh = () => {
    setRefreshing(true);
    setTimeout(() => setRefreshing(false), 1200);
  };

  return (
    <View style={styles.bg}>
      {/* Header with gradient */}
      <LinearGradient colors={["#3FE0E8", "#3578C9"]} style={styles.headerGradient}>
        <View style={styles.headerContent}>
          <TouchableOpacity 
            style={styles.menuButton} 
            onPress={() => navigation.openDrawer()}
          >
            <Text style={styles.menuIcon}>☰</Text>
          </TouchableOpacity>
          <Text style={styles.headerText}>Hello Arciece of JCB</Text>
          <View style={styles.headerRight}>
            <View style={styles.bellWrap}>
              <Text style={styles.bell}>🔔</Text>
              <View style={styles.badge} />
            </View>
            <Image source={require('../assets/PureLogo.png')} style={styles.avatar} />
            <Text style={styles.username}>Arciece</Text>
          </View>
        </View>
      </LinearGradient>
      <ScrollView
        contentContainerStyle={styles.container}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor="#3FE0E8" />}
      >
        {/* Section: Summary */}
        <Text style={styles.sectionHeader}>Summary</Text>
        {summaryData.map((item) => (
          <TouchableOpacity key={item.label} style={styles.summaryCard} activeOpacity={0.85}>
            <LinearGradient colors={["#eaf6fa", "#fff"]} style={styles.summaryGradient}>
              <Text style={styles.summaryIcon}>{item.icon}</Text>
              <Text style={styles.summaryLabel}>{item.label}</Text>
              <Text style={styles.summaryValue}>{item.value}</Text>
              <Text style={styles.summaryChange}>{item.change} <Text style={styles.summarySub}>{item.sub}</Text></Text>
            </LinearGradient>
          </TouchableOpacity>
        ))}
        <View style={styles.sectionDivider} />
        {/* Section: Charts */}
        <Text style={styles.sectionHeader}>Analytics</Text>
        <View style={styles.chartsCol}>
          <View style={styles.chartCard}>
            <Text style={styles.chartTitle}>Order Status</Text>
            <View style={styles.donutChart}>
              <Text style={styles.donutText}>🟦 Delivered: 50\n🟦 Pending: 15\n🟦 Canceled: 5</Text>
            </View>
          </View>
          <View style={styles.chartCard}>
            <Text style={styles.chartTitle}>Total Sales</Text>
            <View style={styles.lineChart}>
              <Text style={styles.lineText}>📈 Jan: 5000\nFeb: 3000\nMar: 3500</Text>
            </View>
          </View>
        </View>
        <View style={styles.sectionDivider} />
        {/* Section: Info Cards */}
        <Text style={styles.sectionHeader}>Quick Info</Text>
        {infoCards.map((item) => (
          <TouchableOpacity key={item.label} style={styles.infoCard} activeOpacity={0.85}>
            <Text style={styles.infoIcon}>{item.icon}</Text>
            <Text style={styles.infoLabel}>{item.label}</Text>
            <Text style={styles.infoValue}>{item.value}</Text>
          </TouchableOpacity>
        ))}
        <View style={styles.remainingCard}>
          <Text style={styles.infoLabel}>Remaining Containers</Text>
          <View style={styles.remainingRow}>
            <View style={styles.remainingCol}><Text>Container 1</Text><Text style={styles.remainingValue}>45</Text></View>
            <View style={styles.remainingCol}><Text>Container 2</Text><Text style={styles.remainingValue}>30</Text></View>
          </View>
        </View>
      </ScrollView>
      {/* Floating Action Button */}
      <TouchableOpacity style={styles.fab} activeOpacity={0.8} onPress={() => alert('Quick Action!')}>
        <Text style={styles.fabIcon}>＋</Text>
      </TouchableOpacity>
    </View>
  );
}

const styles = StyleSheet.create({
  bg: { flex: 1, backgroundColor: '#f4f7fb' },
  headerGradient: { paddingTop: Platform.OS === 'ios' ? 54 : 32, paddingBottom: 18, borderBottomLeftRadius: 24, borderBottomRightRadius: 24, elevation: 4 },
  headerContent: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', paddingHorizontal: 16 },
  menuButton: { padding: 8 },
  menuIcon: { fontSize: 24, color: '#fff', fontWeight: 'bold' },
  headerText: { fontSize: 19, fontWeight: 'bold', color: '#fff', letterSpacing: 0.2 },
  headerRight: { flexDirection: 'row', alignItems: 'center' },
  bellWrap: { position: 'relative', marginRight: 10 },
  bell: { fontSize: 28, color: '#fff' },
  badge: { position: 'absolute', top: 2, right: 2, width: 10, height: 10, borderRadius: 5, backgroundColor: '#ff3b30', borderWidth: 1, borderColor: '#fff' },
  avatar: { width: 36, height: 36, borderRadius: 18, backgroundColor: '#fff', marginRight: 6, borderWidth: 2, borderColor: '#3FE0E8' },
  username: { fontSize: 17, color: '#fff', fontWeight: '600' },
  container: { padding: 18, paddingBottom: 100 },
  sectionHeader: { fontSize: 21, fontWeight: 'bold', color: '#3578C9', marginBottom: 14, marginTop: 22, letterSpacing: 0.2 },
  sectionDivider: { height: 1, backgroundColor: '#e0eaf0', marginVertical: 18, borderRadius: 1 },
  summaryCard: { width: '100%', backgroundColor: '#fff', borderRadius: 22, marginBottom: 18, elevation: 4, shadowColor: '#3FE0E8', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.12, shadowRadius: 8, alignItems: 'center', padding: 22 },
  summaryGradient: { flex: 1, alignItems: 'center', borderRadius: 22, width: '100%', height: '100%', justifyContent: 'center' },
  summaryIcon: { fontSize: 36, marginBottom: 2 },
  summaryLabel: { fontSize: 17, color: '#888', fontWeight: '500' },
  summaryValue: { fontSize: 28, fontWeight: 'bold', color: '#222', marginVertical: 2 },
  summaryChange: { fontSize: 17, color: '#1db954', marginTop: 2 },
  summarySub: { color: '#888', fontWeight: 'normal' },
  chartsCol: { flexDirection: 'column', gap: 22 },
  chartCard: { backgroundColor: '#fff', borderRadius: 22, padding: 22, marginBottom: 18, elevation: 3, shadowColor: '#3578C9', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.09, shadowRadius: 6 },
  chartTitle: { fontSize: 19, fontWeight: 'bold', color: '#222', marginBottom: 14 },
  donutChart: { height: 130, width: 130, justifyContent: 'center', alignItems: 'center', backgroundColor: '#eaf6fa', borderRadius: 65, marginBottom: 8, alignSelf: 'center' },
  donutText: { fontSize: 16, color: '#3578C9', textAlign: 'center', fontWeight: '500' },
  lineChart: { height: 130, justifyContent: 'center', alignItems: 'center', backgroundColor: '#eaf6fa', borderRadius: 16 },
  lineText: { fontSize: 16, color: '#3578C9', textAlign: 'center', fontWeight: '500' },
  infoCol: { flexDirection: 'column', gap: 18 },
  infoCard: { backgroundColor: '#fff', borderRadius: 22, padding: 26, elevation: 3, shadowColor: '#3FE0E8', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.09, shadowRadius: 6, alignItems: 'center', marginBottom: 14 },
  infoIcon: { fontSize: 32, marginBottom: 2 },
  infoLabel: { fontSize: 18, color: '#888', fontWeight: '500' },
  infoValue: { fontSize: 28, fontWeight: 'bold', color: '#222' },
  remainingCard: { backgroundColor: '#fff', borderRadius: 22, padding: 26, elevation: 3, shadowColor: '#3FE0E8', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.09, shadowRadius: 6, marginBottom: 14 },
  remainingRow: { flexDirection: 'row', justifyContent: 'space-between', marginTop: 14 },
  remainingCol: { alignItems: 'center', flex: 1 },
  remainingValue: { fontSize: 28, fontWeight: 'bold', color: '#3578C9' },
  fab: { position: 'absolute', right: 28, bottom: 28, backgroundColor: '#3FE0E8', width: 62, height: 62, borderRadius: 31, justifyContent: 'center', alignItems: 'center', elevation: 6, shadowColor: '#3578C9', shadowOffset: { width: 0, height: 4 }, shadowOpacity: 0.18, shadowRadius: 8 },
  fabIcon: { color: '#fff', fontSize: 36, fontWeight: 'bold' },
});
