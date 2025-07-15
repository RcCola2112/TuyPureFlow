import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet, ScrollView, TouchableOpacity, Modal, TextInput, Platform } from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons, MaterialIcons } from '@expo/vector-icons';

export default function InventoryScreen({ route }) {
  const [modalVisible, setModalVisible] = useState(false);
  const [items, setItems] = useState([]);
  const [itemName, setItemName] = useState('');
  const [itemQty, setItemQty] = useState('');
  const [editModalVisible, setEditModalVisible] = useState(false);
  const [editIndex, setEditIndex] = useState(null);
  const [editName, setEditName] = useState('');
  const [editQty, setEditQty] = useState('');
  const [containers, setContainers] = useState([
    { name: 'Container 1', stock: 45, damaged: 0 },
    { name: 'Container 2', stock: 30, damaged: 0 },
  ]);
  const [containerModalVisible, setContainerModalVisible] = useState(false);
  const [newContainerName, setNewContainerName] = useState('');
  const [newContainerType, setNewContainerType] = useState('Container');
  const [newContainerPrice, setNewContainerPrice] = useState('');
  const [editContainerIndex, setEditContainerIndex] = useState(null);
  const [editContainerName, setEditContainerName] = useState('');
  const [editContainerType, setEditContainerType] = useState('Container');
  const [editContainerPrice, setEditContainerPrice] = useState('');
  const [actionLog, setActionLog] = useState([]);
  const [lastUpdate, setLastUpdate] = useState(new Date());
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [shopId, setShopId] = useState(null);

  // API Base URL - Change this to your computer's IP address
  const API_BASE_URL = 'http://192.168.1.20/pureflowBackend'; // Replace with your actual IP address

  const distributor = route?.params?.distributor;

  useEffect(() => {
    if (distributor && distributor.distributor_id) {
      // Fetch shop_id for this distributor
      fetch(`http://192.168.1.20/pureflowBackend/get_shop_id.php?distributor_id=${distributor.distributor_id}`)
        .then(res => res.json())
        .then(data => {
          if (data.success && data.shop_id) setShopId(data.shop_id);
        });
    }
  }, [distributor]);

  useEffect(() => {
    if (shopId) loadContainers();
  }, [shopId]);

  const loadContainers = async () => {
    if (!shopId) return;
    setLoading(true);
    try {
      const response = await fetch(`${API_BASE_URL}/container.php?shop_id=${shopId}`);
      const data = await response.json();
      
      if (data.success) {
        setContainers(data.containers.map(container => ({
          id: container.container_id,
          name: container.Container_Name, // map Container_Name from backend
          type: container.type,
          price: parseFloat(container.price),
          stock: parseInt(container.stock_quantity),
          damaged: parseInt(container.damaged_quantity)
        })));
      } else {
        setError(data.error || 'Failed to load containers');
      }
    } catch (err) {
      setError('Network error: ' + err.message);
    } finally {
      setLoading(false);
    }
  };

  const handleAddItem = () => {
    if (itemName && itemQty) {
      setItems([...items, { name: itemName, qty: itemQty }]);
      setItemName('');
      setItemQty('');
      setModalVisible(false);
      updateLast();
    }
  };

  const handleEditItem = (idx) => {
    setEditIndex(idx);
    setEditName(items[idx].name);
    setEditQty(items[idx].qty);
    setEditModalVisible(true);
  };

  const handleSaveEdit = () => {
    if (editName && editQty) {
      const updated = [...items];
      updated[editIndex] = { name: editName, qty: editQty };
      setItems(updated);
      setEditModalVisible(false);
      updateLast();
    }
  };

  const handleDeleteItem = (idx) => {
    setItems(items.filter((_, i) => i !== idx));
    updateLast();
  };

  const handleAddContainer = async () => {
    if (!shopId) return;
    if (newContainerName && newContainerPrice !== '' && !isNaN(Number(newContainerPrice))) {
      setLoading(true);
      try {
        const response = await fetch(`${API_BASE_URL}/container.php`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            shop_id: shopId,
            Container_Name: newContainerName, // changed from 'name'
            type: newContainerType,
            price: parseFloat(newContainerPrice),
            stock_quantity: 0,
            damaged_quantity: 0
          })
        });
        
        const data = await response.json();
        
        if (data.success) {
          setNewContainerName('');
          setNewContainerType('Container');
          setNewContainerPrice('');
          setContainerModalVisible(false);
          updateLast();
          loadContainers(); // Reload containers from database
        } else {
          setError(data.error || 'Failed to add container');
        }
      } catch (err) {
        setError('Network error: ' + err.message);
      } finally {
        setLoading(false);
      }
    }
  };

  const handleRemoveContainer = async (idx) => {
    setLoading(true);
    try {
      const response = await fetch(`${API_BASE_URL}/container.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          action: 'delete',
          container_id: containers[idx].id
        })
      });
      const responseText = await response.text();
      let data;
      try {
        data = JSON.parse(responseText);
      } catch (parseError) {
        setError('Invalid JSON response from server: ' + responseText);
        setLoading(false);
        return;
      }
      if (data.success) {
        updateLast();
        loadContainers();
      } else {
        setError(data.error || 'Failed to delete container');
      }
    } catch (err) {
      setError('Network error: ' + err.message);
    } finally {
      setLoading(false);
    }
  };

  const handleEditContainer = (idx) => {
    setEditContainerIndex(idx);
    setEditContainerName(containers[idx].name);
    setEditContainerType(containers[idx].type || 'Container');
    setEditContainerPrice(containers[idx].price ? String(containers[idx].price) : '');
    setContainerModalVisible(true);
  };

  const handleSaveContainerEdit = async () => {
    if (editContainerName && editContainerPrice !== '' && !isNaN(Number(editContainerPrice))) {
      setLoading(true);
      try {
        const response = await fetch(`${API_BASE_URL}/container.php`, {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            container_id: containers[editContainerIndex].id,
            Container_Name: editContainerName, // changed from 'name'
            type: editContainerType,
            price: parseFloat(editContainerPrice),
            stock_quantity: containers[editContainerIndex].stock,
            damaged_quantity: containers[editContainerIndex].damaged
          })
        });
        
        const data = await response.json();
        
        if (data.success) {
          setEditContainerIndex(null);
          setEditContainerName('');
          setEditContainerType('Container');
          setEditContainerPrice('');
          setContainerModalVisible(false);
          updateLast();
          loadContainers(); // Reload containers from database
        } else {
          setError(data.error || 'Failed to update container');
        }
      } catch (err) {
        setError('Network error: ' + err.message);
      } finally {
        setLoading(false);
      }
    }
  };

  // Update stock
  const handleStockChange = async (idx, delta) => {
    const updatedStock = Math.max(0, containers[idx].stock + delta);
    setLoading(true);
    try {
      const response = await fetch(`${API_BASE_URL}/container.php`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          container_id: containers[idx].id,
          Container_Name: containers[idx].name, // changed from 'name'
          type: containers[idx].type,
          price: containers[idx].price,
          stock_quantity: updatedStock,
          damaged_quantity: containers[idx].damaged
        })
      });
      
      const data = await response.json();
      
      if (data.success) {
        setActionLog(log => [
          { type: delta > 0 ? 'Add Stock' : 'Remove Stock', msg: `${delta > 0 ? 'Added' : 'Removed'} ${Math.abs(delta)} to ${containers[idx].name}`, time: new Date() },
          ...log
        ]);
        updateLast();
        loadContainers(); // Reload containers from database
      } else {
        setError(data.error || 'Failed to update stock');
      }
    } catch (err) {
      setError('Network error: ' + err.message);
    } finally {
      setLoading(false);
    }
  };

  // Update damaged
  const handleDamagedChange = async (idx, delta) => {
    setLoading(true);
    try {
      let updatedStock = containers[idx].stock;
      let updatedDamaged = containers[idx].damaged;
      
      if (delta > 0) {
        updatedDamaged = Math.max(0, updatedDamaged + delta);
        setActionLog(log => [
          { type: 'Mark Damaged', msg: `Marked ${Math.abs(delta)} as damaged in ${containers[idx].name}`, time: new Date() },
          ...log
        ]);
      } else if (delta < 0 && updatedDamaged > 0) {
        updatedDamaged = Math.max(0, updatedDamaged + delta);
        updatedStock = updatedStock + Math.abs(delta);
        setActionLog(log => [
          { type: 'Restore', msg: `Restored ${Math.abs(delta)} from damaged to stock in ${containers[idx].name}`, time: new Date() },
          ...log
        ]);
      }
      
      const response = await fetch(`${API_BASE_URL}/container.php`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          container_id: containers[idx].id,
          Container_Name: containers[idx].name, // changed from 'name'
          type: containers[idx].type,
          price: containers[idx].price,
          stock_quantity: updatedStock,
          damaged_quantity: updatedDamaged
        })
      });
      
      const data = await response.json();
      
      if (data.success) {
        updateLast();
        loadContainers(); // Reload containers from database
      } else {
        setError(data.error || 'Failed to update damaged quantity');
      }
    } catch (err) {
      setError('Network error: ' + err.message);
    } finally {
      setLoading(false);
    }
  };

  // Update lastUpdate on any inventory change
  const updateLast = () => setLastUpdate(new Date());

  return (
    <View style={styles.container}>
      <LinearGradient colors={["#3FE0E8", "#3578C9"]} style={styles.header}>
        <Text style={styles.headerTitle}>Inventory</Text>
        <Text style={styles.lastUpdate}>Last Update: {lastUpdate.toLocaleString()}</Text>
      </LinearGradient>
      <ScrollView style={styles.content} contentContainerStyle={{ paddingBottom: 40 }}>
        {error ? (
          <View style={styles.errorCard}>
            <Text style={styles.errorText}>{error}</Text>
            <TouchableOpacity style={styles.retryButton} onPress={() => setError('')}>
              <Text style={styles.retryButtonText}>Dismiss</Text>
            </TouchableOpacity>
          </View>
        ) : null}
        
        {loading ? (
          <View style={styles.loadingCard}>
            <Text style={styles.loadingText}>Loading...</Text>
          </View>
        ) : null}
        
        <View style={styles.card}>
          <Text style={styles.sectionHeader}>Container Management</Text>
          <TouchableOpacity style={styles.fullButton} onPress={() => { setEditContainerIndex(null); setContainerModalVisible(true); }}>
            <Ionicons name="add-circle" size={22} color="#fff" style={{ marginRight: 8 }} />
            <Text style={styles.fullButtonText}>Add Container</Text>
          </TouchableOpacity>
          {containers.map((container, idx) => (
            <View key={container.id || idx} style={styles.itemRow}>
              <Text style={styles.cardText}>{container.name} <Text style={styles.typeText}>({container.type || 'Container'})</Text>: ₱{container.price?.toFixed(2) || '0.00'} | {container.stock} units <Text style={styles.damagedText}>(Damaged: {container.damaged})</Text></Text>
              <View style={styles.actionRow}>
                <TouchableOpacity onPress={() => handleStockChange(idx, 1)} style={styles.iconBtn}><Ionicons name="add" size={18} color="#3FE0E8" /></TouchableOpacity>
                <TouchableOpacity onPress={() => handleStockChange(idx, -1)} style={styles.iconBtn}><Ionicons name="remove" size={18} color="#3FE0E8" /></TouchableOpacity>
                <TouchableOpacity onPress={() => handleDamagedChange(idx, 1)} style={styles.iconBtn}><MaterialIcons name="report-problem" size={18} color="#ff3b30" /></TouchableOpacity>
                <TouchableOpacity onPress={() => handleDamagedChange(idx, -1)} style={styles.iconBtn}><Ionicons name="refresh" size={18} color="#3578C9" /></TouchableOpacity>
                <TouchableOpacity onPress={() => handleEditContainer(idx)} style={styles.iconBtn}><MaterialIcons name="edit" size={18} color="#3578C9" /></TouchableOpacity>
                <TouchableOpacity onPress={() => handleRemoveContainer(idx)} style={[styles.iconBtn, { backgroundColor: '#ff3b30' }]}><MaterialIcons name="delete" size={18} color="#fff" /></TouchableOpacity>
              </View>
            </View>
          ))}
        </View>
        <View style={styles.card}>
          <Text style={styles.sectionHeader}>Current Stock</Text>
          {containers.map((container, idx) => (
            <Text key={container.id || idx} style={styles.cardText}>• {container.name}: {container.stock} units available</Text>
          ))}
          <Text style={styles.cardText}>• Damaged Containers: {containers.reduce((sum, c) => sum + c.damaged, 0)} units</Text>
        </View>
      </ScrollView>
      {/* Inventory Action Log */}
      {actionLog.length > 0 && (
        <View style={styles.logCard}>
          <Text style={styles.sectionHeader}>Inventory Log</Text>
          <ScrollView style={{ maxHeight: 120 }}>
            {actionLog.slice(0, 10).map((log, idx) => (
              <Text key={idx} style={styles.logText}>{log.type}: {log.msg} ({log.time.toLocaleTimeString()})</Text>
            ))}
          </ScrollView>
        </View>
      )}
      {/* Container Modal (Add/Rename) */}
      <Modal
        visible={containerModalVisible}
        animationType="slide"
        transparent
        onRequestClose={() => setContainerModalVisible(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={styles.modalContent}>
            <Text style={styles.modalTitle}>{editContainerIndex !== null ? 'Rename Container' : 'Add New Container'}</Text>
            <TextInput
              style={styles.input}
              placeholder="Container Name"
              value={editContainerIndex !== null ? editContainerName : newContainerName}
              onChangeText={editContainerIndex !== null ? setEditContainerName : setNewContainerName}
            />
            <TextInput
              style={styles.input}
              placeholder="Price (₱)"
              value={editContainerIndex !== null ? editContainerPrice : newContainerPrice}
              onChangeText={editContainerIndex !== null ? setEditContainerPrice : setNewContainerPrice}
              keyboardType="numeric"
            />
            {/* Type Selection */}
            <View style={{ flexDirection: 'row', marginBottom: 14, alignItems: 'center' }}>
              <Text style={{ marginRight: 10, fontSize: 16 }}>Type:</Text>
              <TouchableOpacity
                style={[styles.typeBtn, (editContainerIndex !== null ? editContainerType : newContainerType) === 'Container' && styles.typeBtnActive]}
                onPress={() => editContainerIndex !== null ? setEditContainerType('Container') : setNewContainerType('Container')}
              >
                <Text style={styles.typeBtnText}>Container</Text>
              </TouchableOpacity>
              <TouchableOpacity
                style={[styles.typeBtn, (editContainerIndex !== null ? editContainerType : newContainerType) === 'Container with Faucet' && styles.typeBtnActive]}
                onPress={() => editContainerIndex !== null ? setEditContainerType('Container with Faucet') : setNewContainerType('Container with Faucet')}
              >
                <Text style={styles.typeBtnText}>Container with Faucet</Text>
              </TouchableOpacity>
            </View>
            <View style={styles.modalActions}>
              <TouchableOpacity style={styles.modalButton} onPress={editContainerIndex !== null ? handleSaveContainerEdit : handleAddContainer}>
                <Text style={styles.modalButtonText}>{editContainerIndex !== null ? 'Save' : 'Add'}</Text>
              </TouchableOpacity>
              <TouchableOpacity style={[styles.modalButton, { backgroundColor: '#ccc' }]} onPress={() => { setContainerModalVisible(false); setEditContainerIndex(null); setEditContainerName(''); setNewContainerName(''); setEditContainerType('Container'); setNewContainerType('Container'); setEditContainerPrice(''); setNewContainerPrice(''); }}>
                <Text style={[styles.modalButtonText, { color: '#3578C9' }]}>Cancel</Text>
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </Modal>
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
  lastUpdate: {
    color: '#fff',
    fontSize: 13,
    marginTop: 2,
    marginBottom: 2,
    fontStyle: 'italic',
    textAlign: 'right',
    opacity: 0.85,
  },
  content: {
    flex: 1,
    padding: 20,
  },
  card: {
    backgroundColor: '#fff',
    borderRadius: 18,
    padding: 20,
    marginBottom: 22,
    elevation: 3,
    shadowColor: '#3578C9',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.09,
    shadowRadius: 6,
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
  addButton: {
    backgroundColor: '#3FE0E8',
    borderRadius: 22,
    paddingVertical: 14,
    alignItems: 'center',
    marginBottom: 18,
    elevation: 2,
  },
  addButtonText: {
    color: '#fff',
    fontSize: 18,
    fontWeight: 'bold',
  },
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.3)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  modalContent: {
    backgroundColor: '#fff',
    borderRadius: 18,
    padding: 24,
    width: '85%',
    elevation: 5,
    shadowColor: '#3578C9',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.15,
    shadowRadius: 8,
  },
  modalTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#3578C9',
    marginBottom: 18,
    textAlign: 'center',
  },
  input: {
    borderWidth: 1,
    borderColor: '#e0eaf0',
    borderRadius: 10,
    padding: Platform.OS === 'ios' ? 14 : 10,
    marginBottom: 14,
    fontSize: 16,
  },
  modalActions: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginTop: 10,
  },
  modalButton: {
    backgroundColor: '#3FE0E8',
    borderRadius: 10,
    paddingVertical: 10,
    paddingHorizontal: 24,
    marginHorizontal: 4,
  },
  modalButtonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: 'bold',
  },
  itemRow: {
    flexDirection: 'column', // Changed to column for better spacing
    marginBottom: 14,
    paddingVertical: 10,
    minHeight: 48,
  },
  itemActions: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 10,
  },
  actionBtn: {
    backgroundColor: '#eaf6fa',
    borderRadius: 8,
    paddingVertical: 4,
    paddingHorizontal: 12,
    marginLeft: 6,
  },
  actionBtnText: {
    color: '#3578C9',
    fontWeight: 'bold',
    fontSize: 15,
  },
  sectionHeader: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#3578C9',
    marginBottom: 12,
    marginTop: 2,
    letterSpacing: 0.2,
  },
  fullButton: {
    flexDirection: 'row',
    backgroundColor: '#3FE0E8',
    borderRadius: 28,
    paddingVertical: 14,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 18,
    elevation: 2,
    width: '100%',
    marginTop: 4,
    shadowColor: '#3FE0E8',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.12,
    shadowRadius: 8,
  },
  fullButtonText: {
    color: '#fff',
    fontSize: 18,
    fontWeight: 'bold',
    letterSpacing: 0.5,
  },
  iconBtn: {
    backgroundColor: '#eaf6fa',
    borderRadius: 16,
    padding: 6,
    marginLeft: 4,
    marginRight: 2,
    elevation: 1,
    alignItems: 'center',
    justifyContent: 'center',
    minWidth: undefined,
    minHeight: undefined,
    shadowColor: undefined,
    shadowOffset: undefined,
    shadowOpacity: undefined,
    shadowRadius: undefined,
  },
  damagedText: {
    color: '#ff3b30',
    fontWeight: 'bold',
  },
  logCard: {
    backgroundColor: '#f7fbfd',
    borderRadius: 16,
    padding: 16,
    marginBottom: 18,
    elevation: 1,
    shadowColor: '#3578C9',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.08,
    shadowRadius: 3,
  },
  logText: {
    fontSize: 14,
    color: '#888',
    marginBottom: 2,
  },
  typeText: {
    color: '#3578C9',
    fontWeight: 'bold',
    fontSize: 15,
    marginLeft: 4,
  },
  typeBtn: {
    backgroundColor: '#eaf6fa',
    borderRadius: 8,
    paddingVertical: 6,
    paddingHorizontal: 12,
    marginHorizontal: 2,
  },
  typeBtnActive: {
    backgroundColor: '#3FE0E8',
  },
  typeBtnText: {
    color: '#3578C9',
    fontWeight: 'bold',
    fontSize: 15,
  },
  actionRow: {
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    marginTop: 8,
    marginBottom: 8,
    gap: 8,
  },
  errorCard: {
    backgroundColor: '#ffebee',
    borderRadius: 12,
    padding: 16,
    marginBottom: 16,
    borderLeftWidth: 4,
    borderLeftColor: '#f44336',
  },
  errorText: {
    color: '#c62828',
    fontSize: 14,
    marginBottom: 8,
  },
  retryButton: {
    backgroundColor: '#f44336',
    borderRadius: 8,
    paddingVertical: 8,
    paddingHorizontal: 16,
    alignSelf: 'flex-start',
  },
  retryButtonText: {
    color: '#fff',
    fontSize: 14,
    fontWeight: 'bold',
  },
  loadingCard: {
    backgroundColor: '#e3f2fd',
    borderRadius: 12,
    padding: 16,
    marginBottom: 16,
    alignItems: 'center',
  },
  loadingText: {
    color: '#1976d2',
    fontSize: 16,
    fontWeight: 'bold',
  },
}); 