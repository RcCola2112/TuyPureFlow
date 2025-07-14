import React, { useState } from 'react';
import { View, Text, Image, StyleSheet, TouchableOpacity, ScrollView } from 'react-native';
import { FontAwesome } from '@expo/vector-icons';
import ConsumerNavBar from './ConsumerNavBar';
import { useNavigation } from '@react-navigation/native';

export default function ShopDetailScreen({ route }) {
  const { product } = route.params;
  const navigation = useNavigation();
  const [search, setSearch] = useState('');
  const [quantity, setQuantity] = useState(1);

  return (
    <View style={{ flex: 1, backgroundColor: '#fff' }}>
      <ConsumerNavBar navigation={navigation} search={search} setSearch={setSearch} style={styles.fixedNavBar} />
      <View style={{ flex: 1 }}>
        <ScrollView contentContainerStyle={styles.container}>
          <View style={styles.card}>
            <View style={styles.row}>
              <Image source={product.image} style={styles.productImg} resizeMode="contain" />
              <View style={styles.infoCol}>
                <Text style={styles.title}>{product.container ? `${product.shop}'s ${product.container}` : product.shop}</Text>
                <View style={styles.rowCenter}>
                  {[...Array(5)].map((_, i) => (
                    <FontAwesome key={i} name="star" size={22} color="#FFD700" style={{ marginRight: 2 }} />
                  ))}
                  <Text style={styles.ratingText}>50 Rating</Text>
                  <Text style={styles.soldText}>| 100 Sold</Text>
                </View>
                <Text style={styles.detailText}><Text style={styles.detailLabel}>Special Shipping:</Text> {product.special || 20}</Text>
                <Text style={styles.detailText}><Text style={styles.detailLabel}>Refill Price:</Text> <Text style={styles.priceText}>{product.refill || 30}</Text></Text>
                <Text style={styles.detailText}><Text style={styles.detailLabel}>Purchase Container :</Text> {product.retail || 50}</Text>
                <View style={[styles.rowCenter, { marginTop: 10 }]}>
                  <Text style={styles.detailLabel}>Quantity:</Text>
                  <TouchableOpacity style={styles.qtyBtn} onPress={() => setQuantity(Math.max(1, quantity - 1))}><Text style={styles.qtyBtnText}>-</Text></TouchableOpacity>
                  <Text style={styles.qtyNum}>{quantity.toString().padStart(2, '0')}</Text>
                  <TouchableOpacity style={styles.qtyBtn} onPress={() => setQuantity(quantity + 1)}><Text style={styles.qtyBtnText}>+</Text></TouchableOpacity>
                </View>
                <View style={styles.rowCenter}>
                  <TouchableOpacity style={styles.addCartBtn}><Text style={styles.addCartText}>Add to cart</Text></TouchableOpacity>
                  <TouchableOpacity style={styles.buyNowBtn} onPress={() => navigation.navigate('CartCheckout', { product, quantity })}>
                    <Text style={styles.buyNowText}>Buy Now</Text>
                  </TouchableOpacity>
                </View>
              </View>
            </View>
            <View style={styles.visitRow}>
              <View style={styles.visitDot} />
              <Text style={styles.visitText}>Visit {product.shop} Here</Text>
            </View>
            <Text style={styles.sectionTitle}>Available in Shop:</Text>
            <View style={styles.shopRow}>
              <Image source={product.image} style={styles.shopImg} />
              <Text style={styles.shopLabel}>{product.container ? `${product.shop}'s ${product.container === 'Container 1' ? 'Container 2' : 'Container 1'}` : ''}</Text>
            </View>
          </View>
        </ScrollView>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  fixedNavBar: {
    zIndex: 10,
    elevation: 4,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.08,
    shadowRadius: 4,
    borderBottomWidth: 1,
    borderBottomColor: '#eee',
  },
  container: { flexGrow: 1, justifyContent: 'center', alignItems: 'center', backgroundColor: '#fff', padding: 16 },
  card: { backgroundColor: '#fff', borderRadius: 16, padding: 24, width: '100%', maxWidth: 600, elevation: 3, shadowColor: '#3578C9', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.09, shadowRadius: 6 },
  row: { flexDirection: 'row', alignItems: 'flex-start', marginBottom: 18 },
  infoCol: { flex: 1, marginLeft: 18 },
  title: { fontSize: 24, fontWeight: 'bold', color: '#222', marginBottom: 8 },
  rowCenter: { flexDirection: 'row', alignItems: 'center', marginBottom: 8 },
  ratingText: { fontSize: 16, color: '#222', marginLeft: 8 },
  soldText: { fontSize: 16, color: '#222', marginLeft: 8 },
  detailText: { fontSize: 16, color: '#222', marginBottom: 2 },
  detailLabel: { fontWeight: 'bold', color: '#222' },
  priceText: { color: '#3FE0E8', fontWeight: 'bold' },
  qtyBtn: { backgroundColor: '#f4f7fb', borderRadius: 6, paddingHorizontal: 12, paddingVertical: 4, marginHorizontal: 6 },
  qtyBtnText: { fontSize: 18, color: '#3578C9', fontWeight: 'bold' },
  qtyNum: { fontSize: 18, color: '#222', fontWeight: 'bold', minWidth: 28, textAlign: 'center' },
  addCartBtn: { backgroundColor: '#fff', borderWidth: 1, borderColor: '#3FE0E8', borderRadius: 8, paddingVertical: 8, paddingHorizontal: 22, marginRight: 10, marginTop: 10 },
  addCartText: { color: '#3FE0E8', fontWeight: 'bold', fontSize: 16 },
  buyNowBtn: { backgroundColor: '#3FE0E8', borderRadius: 8, paddingVertical: 8, paddingHorizontal: 22, marginTop: 10 },
  buyNowText: { color: '#fff', fontWeight: 'bold', fontSize: 16 },
  productImg: { width: 140, height: 180, borderRadius: 12, backgroundColor: '#eaf6fa' },
  visitRow: { flexDirection: 'row', alignItems: 'center', marginTop: 10, marginBottom: 8 },
  visitDot: { width: 16, height: 16, borderRadius: 8, backgroundColor: '#ccc', marginRight: 8 },
  visitText: { fontSize: 16, color: '#888' },
  sectionTitle: { fontSize: 18, fontWeight: 'bold', color: '#3578C9', marginTop: 12, marginBottom: 6 },
  shopRow: { flexDirection: 'row', alignItems: 'center', marginTop: 4 },
  shopImg: { width: 40, height: 40, borderRadius: 8, marginRight: 10 },
  shopLabel: { fontSize: 15, color: '#3578C9', fontWeight: 'bold' },
}); 