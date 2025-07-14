import React from 'react';
import { View, Text, StyleSheet, ScrollView } from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';

export default function AnalyticsScreen() {
  return (
    <View style={styles.container}>
      <LinearGradient colors={["#3FE0E8", "#3578C9"]} style={styles.header}>
        <Text style={styles.headerTitle}>Analytics</Text>
      </LinearGradient>
      <ScrollView style={styles.content}>
        <View style={styles.card}>
          <Text style={styles.cardTitle}>Sales Analytics</Text>
          <Text style={styles.cardText}>Detailed insights into your business performance.</Text>
        </View>
        <View style={styles.card}>
          <Text style={styles.cardTitle}>Key Metrics</Text>
          <Text style={styles.cardText}>• Total Sales: ₱ 23,456</Text>
          <Text style={styles.cardText}>• Orders: 1,456</Text>
          <Text style={styles.cardText}>• Revenue Today: ₱ 838</Text>
        </View>
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f4f7fb',
  },
  header: {
    paddingTop: 50,
    paddingBottom: 20,
    paddingHorizontal: 20,
  },
  headerTitle: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#fff',
  },
  content: {
    flex: 1,
    padding: 20,
  },
  card: {
    backgroundColor: '#fff',
    borderRadius: 12,
    padding: 20,
    marginBottom: 15,
    elevation: 2,
    shadowColor: '#3578C9',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
  },
  cardTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#3578C9',
    marginBottom: 10,
  },
  cardText: {
    fontSize: 16,
    color: '#666',
    lineHeight: 24,
  },
}); 