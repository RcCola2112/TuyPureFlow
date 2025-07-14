import React, { useState } from 'react';
import { View, Text, StyleSheet, ScrollView, TouchableOpacity, Modal, TextInput, Platform } from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Ionicons, MaterialIcons } from '@expo/vector-icons';

export default function InventoryScreen() {
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

  const handleAddContainer = () => {
    if (newContainerName && newContainerPrice !== '' && !isNaN(Number(newContainerPrice))) {
      setContainers([...containers, { name: newContainerName, type: newContainerType, price: parseFloat(newContainerPrice), stock: 0, damaged: 0 }]);
      setNewContainerName('');
      setNewContainerType('Container');
      setNewContainerPrice('');
      setContainerModalVisible(false);
      updateLast();
    }
  };

  const handleRemoveContainer = (idx) => {
    setContainers(containers.filter((_, i) => i !== idx));
    updateLast();
  };

  const handleEditContainer = (idx) => {
    setEditContainerIndex(idx);
    setEditContainerName(containers[idx].name);
    setEditContainerType(containers[idx].type || 'Container');
    setEditContainerPrice(containers[idx].price ? String(containers[idx].price) : '');
    setContainerModalVisible(true);
  };

  const handleSaveContainerEdit = () => {
    if (editContainerName && editContainerPrice !== '' && !isNaN(Number(editContainerPrice))) {
      const updated = [...containers];
      updated[editContainerIndex].name = editContainerName;
      updated[editContainerIndex].type = editContainerType;
      updated[editContainerIndex].price = parseFloat(editContainerPrice);
      setContainers(updated);
      setEditContainerIndex(null);
      setEditContainerName('');
      setEditContainerType('Container');
      setEditContainerPrice('');
      setContainerModalVisible(false);
      updateLast();
    }
  };

  // Update stock
  const handleStockChange = (idx, delta) => {
    setContainers(prev => {
      const updated = [...prev];
      updated[idx].stock = Math.max(0, updated[idx].stock + delta);
      setActionLog(log => [
        { type: delta > 0 ? 'Add Stock' : 'Remove Stock', msg: `${delta > 0 ? 'Added' : 'Removed'} ${Math.abs(delta)} to ${updated[idx].name}`, time: new Date() },
        ...log
      ]);
      updateLast();
      return updated;
    });
  };

  // Update damaged
  const handleDamagedChange = (idx, delta) => {
    setContainers(prev => {
      const updated = [...prev];
      if (delta > 0) {
        updated[idx].damaged = Math.max(0, updated[idx].damaged + delta);
        setActionLog(log => [
          { type: 'Mark Damaged', msg: `Marked ${Math.abs(delta)} as damaged in ${updated[idx].name}`, time: new Date() },
          ...log
        ]);
      } else if (delta < 0 && updated[idx].damaged > 0) {
        updated[idx].damaged = Math.max(0, updated[idx].damaged + delta);
        updated[idx].stock = updated[idx].stock + Math.abs(delta); // restore to stock
        setActionLog(log => [
          { type: 'Restore', msg: `Restored ${Math.abs(delta)} from damaged to stock in ${updated[idx].name}`, time: new Date() },
          ...log
        ]);
      }
      updateLast();
      return updated;
    });
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
        <View style={styles.card}>
          <Text style={styles.sectionHeader}>Container Management</Text>
          <TouchableOpacity style={styles.fullButton} onPress={() => { setEditContainerIndex(null); setContainerModalVisible(true); }}>
            <Ionicons name="add-circle" size={22} color="#fff" style={{ marginRight: 8 }} />
            <Text style={styles.fullButtonText}>Add Container</Text>
          </TouchableOpacity>
          {containers.map((container, idx) => (
            <View key={idx} style={styles.itemRow}>
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
            <Text key={idx} style={styles.cardText}>• {container.name}: {container.stock} units available</Text>
          ))}
          <Text style={styles.cardText}>• Damaged Containers: {containers.reduce((sum, c) => sum + c.damaged, 0)} units</Text>
        </View>
        <TouchableOpacity style={styles.fullButton} onPress={() => setModalVisible(true)}>
          <Ionicons name="add-circle" size={22} color="#fff" style={{ marginRight: 8 }} />
          <Text style={styles.fullButtonText}>Add Item</Text>
        </TouchableOpacity>
        {/* List of Added Items */}
        {items.length > 0 && (
          <View style={styles.card}>
            <Text style={styles.cardTitle}>Added Items</Text>
            {items.map((item, idx) => (
              <View key={idx} style={styles.itemRow}>
                <Text style={styles.cardText}>• {item.name}: {item.qty} units</Text>
                <View style={styles.itemActions}>
                  <TouchableOpacity onPress={() => handleEditItem(idx)} style={styles.actionBtn}><Text style={styles.actionBtnText}>Edit</Text></TouchableOpacity>
                  <TouchableOpacity onPress={() => handleDeleteItem(idx)} style={[styles.actionBtn, { backgroundColor: '#ff3b30' }]}><Text style={[styles.actionBtnText, { color: '#fff' }]}>Delete</Text></TouchableOpacity>
                </View>
              </View>
            ))}
          </View>
        )}
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
      {/* Modal for Adding Item */}
      <Modal
        visible={modalVisible}
        animationType="slide"
        transparent
        onRequestClose={() => setModalVisible(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={styles.modalContent}>
            <Text style={styles.modalTitle}>Add New Item</Text>
            <TextInput
              style={styles.input}
              placeholder="Item Name"
              value={itemName}
              onChangeText={setItemName}
            />
            <TextInput
              style={styles.input}
              placeholder="Quantity"
              value={itemQty}
              onChangeText={setItemQty}
              keyboardType="numeric"
            />
            <View style={styles.modalActions}>
              <TouchableOpacity style={styles.modalButton} onPress={handleAddItem}>
                <Text style={styles.modalButtonText}>Add</Text>
              </TouchableOpacity>
              <TouchableOpacity style={[styles.modalButton, { backgroundColor: '#ccc' }]} onPress={() => setModalVisible(false)}>
                <Text style={[styles.modalButtonText, { color: '#3578C9' }]}>Cancel</Text>
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </Modal>
      {/* Edit Modal */}
      <Modal
        visible={editModalVisible}
        animationType="slide"
        transparent
        onRequestClose={() => setEditModalVisible(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={styles.modalContent}>
            <Text style={styles.modalTitle}>Edit Item</Text>
            <TextInput
              style={styles.input}
              placeholder="Item Name"
              value={editName}
              onChangeText={setEditName}
            />
            <TextInput
              style={styles.input}
              placeholder="Quantity"
              value={editQty}
              onChangeText={setEditQty}
              keyboardType="numeric"
            />
            <View style={styles.modalActions}>
              <TouchableOpacity style={styles.modalButton} onPress={handleSaveEdit}>
                <Text style={styles.modalButtonText}>Save</Text>
              </TouchableOpacity>
              <TouchableOpacity style={[styles.modalButton, { backgroundColor: '#ccc' }]} onPress={() => setEditModalVisible(false)}>
                <Text style={[styles.modalButtonText, { color: '#3578C9' }]}>Cancel</Text>
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </Modal>
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
}); 