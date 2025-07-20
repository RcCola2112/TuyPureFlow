import React, { useState } from 'react';
import { View, Text, Image, StyleSheet, TouchableOpacity, FlatList, Dimensions } from 'react-native';
import ConsumerNavBar from './ConsumerNavBar';
import { useNavigation } from '@react-navigation/native';
import GallonsImg from '../assets/Gallons.jpg';

const numColumns = 2;
const CARD_MARGIN = 12;
const CARD_WIDTH = (Dimensions.get('window').width - (numColumns + 1) * CARD_MARGIN) / numColumns;

export default function ShopDetailScreen({ route }) {
  const { shop, user } = route.params;
  const navigation = useNavigation();
  const [search, setSearch] = useState('');
  const [quantity, setQuantity] = useState(1);

  const renderGallon = ({ item: container }) => (
    <View style={styles.card}>
      <Image source={GallonsImg} style={styles.productImg} resizeMode="contain" />
      <Text style={styles.gallonName}>{container.Container_Name}</Text>
      <Text style={styles.gallonType}>{container.container_type}</Text>
      <Text style={styles.gallonPrice}>₱{container.price}</Text>
      <Text style={styles.gallonStock}>Stock: {container.stock_quantity}</Text>
      <View style={styles.buttonRow}>
        <TouchableOpacity style={styles.addCartBtn}><Text style={styles.addCartText}>Add to cart</Text></TouchableOpacity>
        <TouchableOpacity style={styles.buyNowBtn} onPress={() => navigation.navigate('CartCheckout', { product: container, quantity, shop, user })}>
          <Text style={styles.buyNowText}>Buy Now</Text>
        </TouchableOpacity>
      </View>
    </View>
  );

  return (
    <View style={{ flex: 1, backgroundColor: '#f7fafd' }}>
      <ConsumerNavBar navigation={navigation} search={search} setSearch={setSearch} style={styles.fixedNavBar} />
      <View style={styles.stickyShopName}><Text style={styles.shopTitle}>{shop.shop_name}</Text></View>
      <FlatList
        data={shop.containers}
        keyExtractor={container => String(container.container_id)}
        renderItem={renderGallon}
        numColumns={numColumns}
        contentContainerStyle={styles.gridContainer}
        showsVerticalScrollIndicator={false}
      />
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
  stickyShopName: {
    backgroundColor: '#fff',
    paddingVertical: 16,
    alignItems: 'center',
    borderBottomWidth: 1,
    borderBottomColor: '#e0e0e0',
    zIndex: 2,
  },
  shopTitle: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#3578C9',
    textAlign: 'center',
  },
  gridContainer: {
    padding: CARD_MARGIN,
    backgroundColor: '#f7fafd',
    alignItems: 'center',
  },
  card: {
    backgroundColor: '#fff',
    borderRadius: 16,
    padding: 14,
    margin: CARD_MARGIN,
    width: CARD_WIDTH,
    alignItems: 'center',
    elevation: 3,
    shadowColor: '#3578C9',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.10,
    shadowRadius: 6,
  },
  productImg: {
    width: CARD_WIDTH - 20,
    height: CARD_WIDTH - 20,
    borderRadius: 12,
    backgroundColor: '#eaf6fa',
    marginBottom: 10,
  },
  gallonName: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#222',
    marginBottom: 2,
    textAlign: 'center',
  },
  gallonType: {
    fontSize: 14,
    color: '#888',
    marginBottom: 2,
    textAlign: 'center',
  },
  gallonPrice: {
    fontSize: 18,
    color: '#3FE0E8',
    fontWeight: 'bold',
    marginBottom: 2,
    textAlign: 'center',
  },
  gallonStock: {
    fontSize: 13,
    color: '#888',
    marginBottom: 8,
    textAlign: 'center',
  },
  buttonRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginTop: 8,
    width: '100%',
  },
  addCartBtn: {
    backgroundColor: '#fff',
    borderWidth: 1,
    borderColor: '#3FE0E8',
    borderRadius: 8,
    paddingVertical: 6,
    paddingHorizontal: 12,
    marginRight: 6,
    flex: 1,
  },
  addCartText: {
    color: '#3FE0E8',
    fontWeight: 'bold',
    fontSize: 14,
    textAlign: 'center',
  },
  buyNowBtn: {
    backgroundColor: '#3FE0E8',
    borderRadius: 8,
    paddingVertical: 6,
    paddingHorizontal: 12,
    flex: 1,
  },
  buyNowText: {
    color: '#fff',
    fontWeight: 'bold',
    fontSize: 14,
    textAlign: 'center',
  },
}); 