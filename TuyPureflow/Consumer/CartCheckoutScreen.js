import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, Image, ScrollView, Modal, ActivityIndicator, Alert } from 'react-native';
import ConsumerNavBar from './ConsumerNavBar';
import { useNavigation } from '@react-navigation/native';
import { FontAwesome5, AntDesign } from '@expo/vector-icons';

export default function CartCheckoutScreen({ route }) {
  const navigation = useNavigation();
  const [search, setSearch] = useState('');
  // Accept product and quantity from params, or use demo data
  const { product, quantity: initialQty, user, shop } = route.params || {};
  // Remove the hardcoded quantities array and use the selected product
  const [quantity, setQuantity] = useState(initialQty || 1);
  const total = product ? (product.price || product.retail || 0) * quantity : 0;
  const [modalVisible, setModalVisible] = useState(false);
  const [addressModalVisible, setAddressModalVisible] = useState(false);

  // Demo order number and address
  const orderNumber = '#123456';
  const address = 'House Number, Barangay';
  const map = '14°01\'23.8"N 120°43\'32.9"E';

  // Remove handleQty function

  const handleConfirmAddress = () => {
    setAddressModalVisible(false);
  };

  const handleConfirmOrder = async () => {
    if (!product || !user || !shop) {
      Alert.alert('Error', 'Missing product, user, or shop info.');
      return;
    }
    const price = product.price || product.retail || product.refill || 0;
    try {
      const response = await fetch('http://192.168.1.3/pureflowBackend/place_order.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          consumer_id: user.consumer_id,
          shop_id: shop.shop_id,
          container_id: product.container_id,
          quantity: quantity,
          price: price,
          total_price: price * quantity,
        }),
      });
      const result = await response.json();
      if (result.success) {
        Alert.alert('Order Placed', 'Your order has been sent to the distributor.');
        setModalVisible(false);
        navigation.navigate('ConsumerDB');
      } else {
        Alert.alert('Order Failed', result.error || 'Could not place order.');
      }
    } catch (error) {
      Alert.alert('Network Error', 'Unable to connect to the server.');
    }
  };

  return (
    <View style={{ flex: 1, backgroundColor: '#fff' }}>
      <ConsumerNavBar navigation={navigation} search={search} setSearch={setSearch} />
      <ScrollView contentContainerStyle={styles.container}>
        <View style={styles.cartCard}>
          <View style={styles.shopRow}>
            <FontAwesome5 name="store" size={28} color="#3FE0E8" style={{ marginRight: 8 }} />
            <Text style={styles.shopName}>{shop?.shop_name || product?.shop || 'Shop Name'}</Text>
          </View>
          {product && (
            <View style={styles.itemRow}>
              <Image source={product.image || require('../assets/Gallons.jpg')} style={styles.itemImg} />
              <View style={styles.itemInfo}>
                <Text style={styles.itemName}>{product.container || product.Container_Name || 'Container'}</Text>
                {product.container_type && (
                  <Text style={styles.itemType}>Type: {product.container_type}</Text>
                )}
                <Text style={styles.itemPrice}>Refill price: {product.price || product.refill || product.retail || 0}</Text>
              </View>
              <View style={styles.itemControls}>
                <Text style={styles.delivery}>Normal delivery</Text>
                <View style={styles.qtyWrap}>
                  <TouchableOpacity style={styles.qtyBtn} onPress={() => setQuantity(quantity + 1)}>
                    <Text style={styles.qtyBtnText}>+</Text>
                  </TouchableOpacity>
                  <Text style={styles.qtyNum}>{quantity}</Text>
                  <TouchableOpacity style={styles.qtyBtn} onPress={() => setQuantity(Math.max(1, quantity - 1))}>
                    <Text style={styles.qtyBtnText}>-</Text>
                  </TouchableOpacity>
                </View>
                <Text style={styles.price}>Price: {total}</Text>
              </View>
            </View>
          )}
          <View style={styles.divider} />
          <Text style={styles.address}>Address: {address}</Text>
          <Text style={styles.address}>Maps: {map}</Text>
          <View style={styles.bottomRow}>
            <TouchableOpacity style={styles.changeBtn} onPress={() => setAddressModalVisible(true)}><Text style={styles.changeText}>Change</Text></TouchableOpacity>
            <Text style={styles.total}>Total price: {total}</Text>
            <TouchableOpacity style={styles.confirmBtn} onPress={handleConfirmOrder}><Text style={styles.confirmText}>Confirm</Text></TouchableOpacity>
          </View>
        </View>
      </ScrollView>
      <Modal
        visible={modalVisible}
        transparent
        animationType="fade"
        onRequestClose={() => setModalVisible(false)}
      >
        <View style={styles.modalBg}>
          <View style={styles.modalCard}>
            <TouchableOpacity style={styles.modalClose} onPress={() => { setModalVisible(false); navigation.navigate('ConsumerDB'); }}>
              <AntDesign name="close" size={32} color="#222" />
            </TouchableOpacity>
            <Text style={styles.modalText}>Order Number: {orderNumber}</Text>
            <Text style={styles.modalText}>Items Ordered: {(product?.container || product?.Container_Name || 'Container') + ' (x' + quantity + ')'}</Text>
            <Text style={styles.modalText}>Total Amount: ₱{total.toFixed(2)}</Text>
            <Text style={styles.modalText}>Delivery Address: {address}</Text>
            <Text style={styles.modalText}>Estimated Delivery Time: Pending Distributor Approval</Text>
          </View>
        </View>
      </Modal>
      <Modal
        visible={addressModalVisible}
        transparent
        animationType="slide"
        onRequestClose={() => setAddressModalVisible(false)}
      >
        <View style={styles.addressModalContainer}>
          <View style={styles.addressModalContent}>
            <Text style={styles.addressModalTitle}>My Addresses</Text>
            <TouchableOpacity style={styles.addressItem} onPress={handleConfirmAddress}>
              <View style={styles.addressRow}>
                <FontAwesome5 name="dot-circle" size={20} color="#3FE0E8" style={{ marginRight: 15, marginTop: 3 }} />
                <View style={{ flex: 1 }}>
                  <View style={styles.addressNameRow}>
                    <Text style={styles.addressName}>{address}</Text>
                  </View>
                  <Text style={styles.addressText}>{address}</Text>
                </View>
                <TouchableOpacity><Text style={styles.editLink}>Edit</Text></TouchableOpacity>
              </View>
            </TouchableOpacity>
            <View style={styles.modalActions}>
              <TouchableOpacity style={styles.modalButton} onPress={() => setAddressModalVisible(false)}>
                <Text style={styles.cancelButtonText}>Cancel</Text>
              </TouchableOpacity>
              <TouchableOpacity style={[styles.modalButton, styles.confirmAddressButton]} onPress={handleConfirmAddress}>
                <Text style={styles.confirmButtonText}>Confirm</Text>
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </Modal>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flexGrow: 1, justifyContent: 'center', alignItems: 'center', backgroundColor: '#fff', padding: 16 },
  cartCard: { backgroundColor: '#fff', borderRadius: 10, padding: 18, width: '100%', maxWidth: 700, elevation: 2, shadowColor: '#3578C9', shadowOffset: { width: 0, height: 2 }, shadowOpacity: 0.09, shadowRadius: 6, marginTop: 24 },
  shopRow: { flexDirection: 'row', alignItems: 'center', marginBottom: 10 },
  shopName: { fontSize: 22, fontWeight: 'bold', color: '#222' },
  itemRow: { flexDirection: 'row', alignItems: 'center', marginBottom: 8, borderBottomWidth: 1, borderBottomColor: '#eee', paddingBottom: 8 },
  itemImg: { width: 48, height: 60, borderRadius: 8, marginRight: 10 },
  itemInfo: { flex: 1, alignSelf: 'flex-start' },
  itemName: { fontSize: 16, fontWeight: 'bold', color: '#3578C9' },
  itemType: {
    fontSize: 14,
    color: '#888',
    marginBottom: 2,
  },
  itemPrice: { fontSize: 15, color: '#222' },
  itemControls: { alignItems: 'flex-end' },
  delivery: { fontSize: 15, color: '#222', marginBottom: 4 },
  qtyWrap: { flexDirection: 'row', alignItems: 'center', marginVertical: 4 },
  qtyBtn: { backgroundColor: '#3FE0E8', borderRadius: 6, paddingHorizontal: 10, paddingVertical: 2, marginHorizontal: 2 },
  qtyBtnText: { color: '#fff', fontWeight: 'bold', fontSize: 18 },
  qtyNum: { fontSize: 16, color: '#222', fontWeight: 'bold', minWidth: 22, textAlign: 'center', marginHorizontal: 2 },
  price: { fontSize: 15, color: '#222', marginTop: 4 },
  divider: { height: 1, backgroundColor: '#eee', marginVertical: 10 },
  bottomRow: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginTop: 10 },
  changeBtn: { backgroundColor: '#3FE0E8', borderRadius: 16, paddingHorizontal: 24, paddingVertical: 8, marginRight: 10 },
  changeText: { color: '#fff', fontWeight: 'bold', fontSize: 16 },
  total: { fontSize: 17, color: '#222', fontWeight: 'bold', flex: 1, textAlign: 'center' },
  confirmBtn: { backgroundColor: '#3FE0E8', borderRadius: 16, paddingHorizontal: 24, paddingVertical: 8, marginLeft: 10 },
  confirmText: { color: '#fff', fontWeight: 'bold', fontSize: 16 },
  modalBg: { flex: 1, backgroundColor: 'rgba(0,0,0,0.18)', justifyContent: 'center', alignItems: 'center' },
  modalCard: { backgroundColor: '#fff', borderRadius: 12, padding: 24, width: '90%', maxWidth: 400, alignItems: 'flex-start', position: 'relative' },
  modalClose: { position: 'absolute', top: 10, right: 10, zIndex: 2 },
  modalText: { fontSize: 17, color: '#222', marginBottom: 10, marginTop: 10 },
  // Address Modal Styles
  addressModalContainer: {
    flex: 1,
    justifyContent: 'flex-end',
    backgroundColor: 'rgba(0,0,0,0.4)',
  },
  addressModalContent: {
    backgroundColor: '#fff',
    borderTopLeftRadius: 20,
    borderTopRightRadius: 20,
    padding: 22,
  },
  addressModalTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    marginBottom: 20,
  },
  addressItem: {
    borderWidth: 1,
    borderColor: '#eee',
    borderRadius: 8,
    padding: 15,
    marginBottom: 10,
    borderColor: '#3FE0E8',
  },
  addressRow: {
    flexDirection: 'row',
    alignItems: 'flex-start',
  },
  addressNameRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 5,
    flexWrap: 'wrap',
  },
  addressName: {
    fontWeight: 'bold',
    fontSize: 16,
  },
  addressText: {
    fontSize: 14,
    color: '#333',
    flexWrap: 'wrap',
    marginBottom: 2,
  },
  editLink: {
    color: '#3FE0E8',
    fontSize: 16,
    marginLeft: 10,
  },
  modalActions: {
    flexDirection: 'row',
    justifyContent: 'flex-end',
    marginTop: 20,
    paddingTop: 10,
  },
  modalButton: {
    borderRadius: 20,
    paddingVertical: 10,
    paddingHorizontal: 25,
    marginLeft: 10,
    borderWidth: 1,
    borderColor: '#BDBDBD',
  },
  cancelButtonText: {
    color: '#757575',
    textAlign: 'center',
  },
  confirmAddressButton: {
    backgroundColor: '#3FE0E8',
    borderColor: '#3FE0E8',
  },
  confirmButtonText: {
    color: '#fff',
    fontWeight: 'bold',
    textAlign: 'center',
  },
}); 