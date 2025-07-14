import React from 'react';
import { View, Text, StyleSheet } from 'react-native';

export default function FollowingShopScreen() {
  return (
    <View style={styles.container}>
      <Text style={styles.title}>Following Shop</Text>
      <Text style={styles.text}>This is the Following Shop screen.</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, justifyContent: 'center', alignItems: 'center', backgroundColor: '#fff' },
  title: { fontSize: 24, fontWeight: 'bold', color: '#3578C9', marginBottom: 10 },
  text: { fontSize: 16, color: '#444' },
}); 