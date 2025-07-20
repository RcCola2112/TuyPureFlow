import React, { useEffect, useState, useCallback } from 'react';
import { View, Text, StyleSheet, FlatList, TouchableOpacity, Alert, RefreshControl, ScrollView } from 'react-native';
import { Picker } from '@react-native-picker/picker';

export default function OrdersScreen({ route }) {
  const distributor = route?.params?.distributor;
  const initialShop = route?.params?.shop;
  const [shop, setShop] = useState(initialShop);
  const [orders, setOrders] = useState([]);
  const [loading, setLoading] = useState(false);
  const [refreshing, setRefreshing] = useState(false);
  const [filter, setFilter] = useState('All'); // 'All', 'Pending', 'Accepted', 'Rejected'

  // Fetch shop info if not provided
  useEffect(() => {
    if (!shop && distributor?.distributor_id) {
      fetch(`http://192.168.1.3/pureflowBackend/get_shop_id.php?distributor_id=${distributor.distributor_id}`)
        .then(res => res.json())
        .then(data => {
          if (data.success && data.shop_id) {
            setShop({ shop_id: data.shop_id });
          } else {
            Alert.alert('Error', data.message || 'Failed to fetch shop info for distributor.');
          }
        })
        .catch(e => {
          Alert.alert('Network Error', 'Unable to fetch shop info.');
        });
    }
  }, [distributor, shop]);

  const fetchOrders = async () => {
    setLoading(true);
    try {
      if (!shop?.shop_id) throw new Error('Shop ID is missing. Cannot fetch orders.');
      const url = `http://192.168.1.3/pureflowBackend/get_orders.php?shop_id=${shop.shop_id}`;
      const response = await fetch(url);
      if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
      const data = await response.json();
      if (data.success) {
        setOrders(data.orders);
      } else {
        Alert.alert('Error', data.error || 'Failed to fetch orders from server.');
      }
    } catch (e) {
      Alert.alert('Network Error', `Failed to fetch orders: ${e.message}`);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => {
    if (shop && shop.shop_id) {
      fetchOrders();
    }
  }, [shop]);

  const onRefresh = useCallback(() => {
    setRefreshing(true);
    fetchOrders();
  }, [shop]);

  const handleUpdateStatus = async (order_id, status) => {
    // Use only allowed ENUM values
    console.log('Updating order:', order_id, status);
    try {
      const response = await fetch('http://192.168.1.3/pureflowBackend/update_order_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ order_id, status }),
      });
      const result = await response.json();
      if (result.success) {
        Alert.alert('Success', `Order ${status}`);
        fetchOrders();
      } else {
        Alert.alert('Error', result.error || 'Failed to update order.');
      }
    } catch (e) {
      Alert.alert('Error', 'Network error.');
    }
  };

  // Filtering logic
  const filteredOrders = filter === 'All' ? orders : orders.filter(o => (o.status || '').toLowerCase() === filter.toLowerCase());

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Orders</Text>
      {/* Filter Buttons */}
      <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={styles.filterRow} style={styles.filterScroll}>
        {['All', 'Pending', 'Processing', 'Out for Delivery', 'Completed', 'Cancelled'].map(f => (
          <TouchableOpacity
            key={f}
            style={[
              styles.filterBtn,
              filter === f ? styles.filterBtnActive : styles.filterBtnInactive
            ]}
            onPress={() => setFilter(f)}
            activeOpacity={0.85}
          >
            <Text style={[
              styles.filterBtnText,
              filter === f ? styles.filterBtnTextActive : styles.filterBtnTextInactive
            ]} numberOfLines={1} ellipsizeMode="tail">{f}</Text>
          </TouchableOpacity>
        ))}
      </ScrollView>
      <FlatList
        data={filteredOrders}
        keyExtractor={item => String(item.order_id)}
        refreshing={loading}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor="#3FE0E8" />}
        ListEmptyComponent={<Text style={styles.emptyText}>{loading ? 'Loading...' : 'No orders found.'}</Text>}
        renderItem={({ item }) => (
          <View style={styles.card}>
            <Text style={styles.text}>Order #{item.order_id}</Text>
            <Text style={styles.text}>Consumer: {item.consumer_name}</Text>
            <Text style={styles.text}>Status: {item.status}</Text>
            <Text style={styles.text}>Total Price: ₱{item.total_price}</Text>
            <Text style={styles.text}>Items:</Text>
            {item.items.map(orderItem => (
              <View key={orderItem.order_item_id} style={styles.itemRow}>
                <Text style={styles.text}>- {orderItem.Container_Name} x {orderItem.quantity} (₱{orderItem.price})</Text>
              </View>
            ))}
            {/* Status Dropdown for each order */}
            <View style={styles.row}>
              <Picker
                selectedValue={item.status}
                style={styles.statusPicker}
                itemStyle={{ fontSize: 16 }}
                onValueChange={value => handleUpdateStatus(item.order_id, value)}
                mode="dropdown"
              >
                {['Pending','Processing','Out for Delivery','Completed','Cancelled'].map(statusOption => (
                  <Picker.Item key={statusOption} label={statusOption} value={statusOption} />
                ))}
              </Picker>
            </View>
          </View>
        )}
      />
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#fff', padding: 16 },
  title: { fontSize: 22, fontWeight: 'bold', color: '#3578C9', marginBottom: 12 },
  filterScroll: {
    marginHorizontal: 10,
  },
  filterRow: {
    flexDirection: 'row',
    marginBottom: 10,
    marginTop: 6,
    alignItems: 'center',
    paddingVertical: 2,
    justifyContent: 'flex-start',
    minHeight: 48,
  },
  filterBtn: {
    paddingHorizontal: 12,
    paddingVertical: 8,
    borderRadius: 22,
    marginHorizontal: 2,
    borderWidth: 1.5,
    alignItems: 'center',
    justifyContent: 'center',
    elevation: 0,
  },
  filterBtnActive: {
    backgroundColor: '#3FE0E8',
    borderColor: '#3FE0E8',
    shadowColor: '#3FE0E8',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.12,
    shadowRadius: 4,
  },
  filterBtnInactive: {
    backgroundColor: '#f4f7fb',
    borderColor: '#e0eaf0',
  },
  filterBtnText: {
    fontWeight: 'bold',
    fontSize: 16,
    letterSpacing: 0.2,
  },
  filterBtnTextActive: {
    color: '#fff',
  },
  filterBtnTextInactive: {
    color: '#3578C9',
    opacity: 0.85,
  },
  card: { backgroundColor: '#f4f7fb', borderRadius: 12, padding: 16, marginBottom: 14, elevation: 2 },
  text: { fontSize: 16, color: '#222', marginBottom: 2 },
  row: { flexDirection: 'row', marginTop: 20 },
  acceptBtn: { backgroundColor: '#3FE0E8', borderRadius: 8, padding: 10, marginRight: 10 },
  rejectBtn: { backgroundColor: '#ff3b30', borderRadius: 8, padding: 10 },
  btnText: { color: '#fff', fontWeight: 'bold' },
  itemRow: { marginLeft: 10 },
  emptyText: { textAlign: 'center', color: '#888', marginTop: 40, fontSize: 16 },
  statusPicker: {
    minWidth: 180,
    height: 48,
    marginTop: 8,
    marginBottom: 8,
    marginHorizontal: 4,
    backgroundColor: '#f4f7fb',
    borderRadius: 8,
  },
}); 