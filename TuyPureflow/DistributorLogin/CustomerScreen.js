import React from 'react';
import { View, Text, StyleSheet, ScrollView } from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';

export default function CustomerScreen() {
  return (
    <View style={styles.container}>
      <LinearGradient colors={["#3FE0E8", "#3578C9"]} style={styles.header}>
        <Text style={styles.headerTitle}>Customers</Text>
      </LinearGradient>
      <ScrollView style={styles.content}>
        <View style={styles.card}>
          <Text style={styles.cardTitle}>Customer Management</Text>
          <Text style={styles.cardText}>Manage your customer database and relationships.</Text>
        </View>
        <View style={styles.card}>
          <Text style={styles.cardTitle}>Customer Overview</Text>
          <Text style={styles.cardText}>• Total Customers: 156</Text>
          <Text style={styles.cardText}>• Active Customers: 142</Text>
          <Text style={styles.cardText}>• New This Month: 8</Text>
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