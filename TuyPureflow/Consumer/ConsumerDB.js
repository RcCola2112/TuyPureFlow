import React, { useState, useRef, useEffect } from 'react';
import { View, Text, Image, StyleSheet, ScrollView, TouchableOpacity, TextInput, useWindowDimensions, FlatList, Platform, RefreshControl, Dimensions, Modal, Button, ActivityIndicator } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { Ionicons, MaterialIcons, FontAwesome, FontAwesome5, Entypo } from '@expo/vector-icons';
import ConsumerNavBar from './ConsumerNavBar';
import GallonsImg from '../assets/Gallons.jpg';

const filters = [
  'All', 'Near me', 'Top Rating', 'Fastest Delivery', 'Open Now', 'Most Orders', 'Price Range', 'Discounts'
];

const products = [
  {
    id: '1',
    shop: "JCB's Shop",
    container: 'Container 1',
    refill: 30,
    retail: 50,
    special: 20,
    image: require('../assets/Gallons.jpg'),
  },
  {
    id: '2',
    shop: "JCB's Shop",
    container: 'Container 2',
    refill: 30,
    retail: 65,
    special: 20,
    image: require('../assets/Gallons.jpg'),
  },
  {
    id: '3',
    shop: "Roger's Shop",
    container: 'Container 1',
    refill: 30,
    retail: 65,
    special: 20,
    image: require('../assets/Gallons.jpg'),
  },
  {
    id: '4',
    shop: "Roger's Shop",
    container: 'Container 2',
    refill: 30,
    retail: 65,
    special: 20,
    image: require('../assets/Gallons.jpg'),
  },
];

const adImages = [
  require('../assets/Adver.png'),
];

const numColumns = 2;
const CARD_MARGIN = 12;
const CARD_WIDTH = (Dimensions.get('window').width - (numColumns + 1) * CARD_MARGIN) / numColumns;
const BANNER_HEIGHT = 200;

export default function ConsumerDB({ route }) {
  const navigation = useNavigation();
  const user = route?.params?.user;
  const { width } = useWindowDimensions();
  const isTablet = width > 600;
  const [selectedFilter, setSelectedFilter] = useState('All');
  const [search, setSearch] = useState('');
  const [adIndex, setAdIndex] = useState(1);
  const scrollRef = useRef();
  const [language, setLanguage] = useState('English');
  const [shops, setShops] = useState([]);
  const [refreshing, setRefreshing] = useState(false);
  const [messageModalVisible, setMessageModalVisible] = useState(false);
  const [selectedShop, setSelectedShop] = useState(null);
  const [messageText, setMessageText] = useState('');
  const [messageLog, setMessageLog] = useState([]);
  const [loadingMessages, setLoadingMessages] = useState(false);
  const [sending, setSending] = useState(false);

  const fetchShops = () => {
    fetch('http://192.168.1.3/pureflowBackend/all_shops_with_containers.php')
      .then(res => res.json())
      .then(data => {
        if (Array.isArray(data)) setShops(data);
        setRefreshing(false);
      });
  };

  useEffect(() => {
    fetchShops();
  }, []);

  // Prepare carousel data: [last, ...ads, first]
  const carouselAds = [adImages[adImages.length - 1], ...adImages, adImages[0]];

  // On mount, scroll to the first real slide
  useEffect(() => {
    if (scrollRef.current) {
      setTimeout(() => {
        scrollRef.current.scrollTo({ x: width, animated: false });
      }, 10);
    }
  }, [width]);

  // Auto-scroll advertisement banner
  useEffect(() => {
    if (adImages.length <= 1) return;
    const interval = setInterval(() => {
      let next = adIndex + 1;
      if (next > adImages.length) {
        // If at the (cloned) last, jump to first real
        if (scrollRef.current) {
          scrollRef.current.scrollTo({ x: width, animated: false });
        }
        setAdIndex(2); // will be set to 1 in handleMomentumScrollEnd
      } else {
        if (scrollRef.current) {
          scrollRef.current.scrollTo({ x: next * width, animated: true });
        }
        setAdIndex(next);
      }
    }, 2000);
    return () => clearInterval(interval);
  }, [adIndex, width]);

  // When scroll ends, handle infinite loop
  const handleMomentumScrollEnd = (event) => {
    let slide = Math.round(event.nativeEvent.contentOffset.x / width);
    if (slide === 0) {
      // Jump to last real slide
      scrollRef.current.scrollTo({ x: adImages.length * width, animated: false });
      setAdIndex(adImages.length);
    } else if (slide === adImages.length + 1) {
      // Jump to first real slide
      scrollRef.current.scrollTo({ x: width, animated: false });
      setAdIndex(1);
    } else {
      setAdIndex(slide);
    }
  };

  const handleAdPress = () => {
    alert('Advertisement clicked!');
  };

  const openMessageModal = async (shop) => {
    setSelectedShop(shop);
    setMessageModalVisible(true);
    setMessageText('');
    setMessageLog([]);
    setLoadingMessages(true);
    try {
      const res = await fetch(
        `http://192.168.1.3/pureflowBackend/get_messages.php?consumer_id=${user.consumer_id}&distributor_id=${shop.distributor_id}`
      );
      const data = await res.json();
      if (data.success && Array.isArray(data.messages)) {
        setMessageLog(data.messages);
      } else {
        setMessageLog([]);
      }
    } catch (e) {
      setMessageLog([]);
    }
    setLoadingMessages(false);
  };

  const handleSendMessage = async () => {
    if (!user || !selectedShop || !messageText.trim()) return;
    setSending(true);

    // Log the data being sent
    const payload = {
      consumer_id: user.consumer_id,
      distributor_id: selectedShop.distributor_id,
      message: messageText,
      sender: 'consumer',
    };
    console.log('Sending message payload:', payload);

    // Optimistically add the message to the log
    const newMsg = {
      id: Date.now(),
      message: messageText,
      sender: 'consumer',
      sent_at: new Date().toISOString(),
    };
    setMessageLog(prev => [...prev, newMsg]);
    setMessageText('');

    try {
      const res = await fetch('http://192.168.1.3/pureflowBackend/send_message.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
      });
      const data = await res.json();
      console.log('Send message response:', data);
      setTimeout(() => {
        fetch(`http://192.168.1.3/pureflowBackend/get_messages.php?consumer_id=${user.consumer_id}&distributor_id=${selectedShop.distributor_id}`)
          .then(res => res.json())
          .then(data => {
            if (data.success && Array.isArray(data.messages)) {
              setMessageLog(data.messages);
            }
          });
      }, 1000);
    } catch (e) {
      console.log('Send message error:', e);
    }
    setSending(false);
  };

  return (
    <View style={{ flex: 1, backgroundColor: '#fff' }}>
      <ConsumerNavBar navigation={navigation} search={search} setSearch={setSearch} user={user} style={styles.fixedNavBar} />
      <View style={{ flex: 1 }}>
        <FlatList
          data={shops}
          keyExtractor={shop => String(shop.shop_id)}
          renderItem={({ item: shop }) => (
            <View style={styles.shopCard}>
              <TouchableOpacity
                style={{ flex: 1 }}
                onPress={() => navigation.navigate('ShopDetail', { shop, user })}
                activeOpacity={0.85}
              >
                <Image source={GallonsImg} style={styles.shopLogo} resizeMode="contain" />
                <Text style={styles.shopName}>{shop.shop_name}</Text>
                {shop.containers && shop.containers.length > 0 && (
                  <Text style={styles.containerType}>{shop.containers[0].container_type}</Text>
                )}
              </TouchableOpacity>
              <TouchableOpacity
                style={styles.messageBtn}
                onPress={() => openMessageModal(shop)}
              >
                <Text style={styles.messageBtnText}>Message</Text>
              </TouchableOpacity>
            </View>
          )}
          numColumns={numColumns}
          columnWrapperStyle={numColumns > 1 ? { justifyContent: 'space-between' } : null}
          contentContainerStyle={styles.gridContainer}
          refreshControl={<RefreshControl refreshing={refreshing} onRefresh={() => { setRefreshing(true); fetchShops(); }} />}
          showsVerticalScrollIndicator={false}
        />
      </View>
      <Modal
        visible={messageModalVisible}
        transparent
        animationType="slide"
        onRequestClose={() => setMessageModalVisible(false)}
      >
        <View style={styles.modalBg}>
          <View style={styles.modalCard}>
            <Text style={styles.modalTitle}>Message {selectedShop?.shop_name}</Text>
            {loadingMessages ? (
              <ActivityIndicator size="large" color="#007AFF" />
            ) : (
              <FlatList
                data={messageLog}
                keyExtractor={item => String(item.id)}
                renderItem={({ item }) => (
                  <View style={[styles.logMsg, item.sender === 'consumer' ? styles.logMsgRight : styles.logMsgLeft]}>
                    <Text style={{ color: '#222' }}>{item.message}</Text>
                    <Text style={styles.logTime}>{item.sent_at ? new Date(item.sent_at).toLocaleString() : ''}</Text>
                  </View>
                )}
                contentContainerStyle={styles.logContainer}
              />
            )}
            <View style={styles.inputRow}>
              <TextInput
                style={styles.input}
                value={messageText}
                onChangeText={setMessageText}
                placeholder="Type your message..."
                editable={!sending}
              />
              <Button title={sending ? 'Sending...' : 'Send'} onPress={handleSendMessage} disabled={sending || !messageText.trim()} />
            </View>
            <Button title="Close" onPress={() => setMessageModalVisible(false)} />
          </View>
        </View>
      </Modal>
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
  responsiveContainer: {
    width: '100%',
    alignSelf: 'stretch',
  },
  responsiveContainerTablet: {
    maxWidth: 900,
    alignSelf: 'center',
  },
  topNavBar: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 8,
    paddingTop: 8,
    paddingBottom: 2,
    backgroundColor: '#fff',
    borderBottomWidth: 1,
    borderBottomColor: '#eee',
  },
  topNavBarTablet: {
    justifyContent: 'center',
    paddingTop: 2,
    paddingBottom: 2,
  },
  topNavBarTabletPad: {
    paddingTop: 24,
  },
  navBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    marginRight: 16,
    paddingVertical: 2,
  },
  navBtnText: {
    color: '#888',
    fontSize: 16,
  },
  navBtnActive: {
    color: '#3FE0E8',
    fontSize: 16,
    fontWeight: 'bold',
    textDecorationLine: 'underline',
  },
  notifDot: {
    width: 8,
    height: 8,
    borderRadius: 4,
    backgroundColor: '#ff3b30',
    position: 'absolute',
    top: 0,
    right: -6,
    borderWidth: 1,
    borderColor: '#fff',
  },
  userWrap: {
    flexDirection: 'row',
    alignItems: 'center',
    marginLeft: 10,
  },
  userCircle: {
    width: 22,
    height: 22,
    borderRadius: 11,
    backgroundColor: '#ccc',
    marginRight: 5,
  },
  userName: {
    color: '#888',
    fontSize: 16,
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 16,
    paddingTop: 12,
    paddingBottom: 8,
    backgroundColor: '#fff',
  },
  headerTablet: {
    justifyContent: 'center',
    paddingTop: 6,
    paddingBottom: 6,
  },
  headerTabletPad: {
    paddingTop: 16,
  },
  logo: {
    width: 90,
    height: 40,
    marginRight: 10,
  },
  searchBar: {
    flex: 1,
    backgroundColor: '#f4f7fb',
    borderRadius: 20,
    paddingVertical: 10,
    paddingHorizontal: 18,
    fontSize: 16,
    color: '#222',
    borderWidth: 1,
    borderColor: '#e0e0e0',
  },
  iconBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    marginLeft: 10,
  },
  iconLabel: {
    color: '#3FE0E8',
    fontSize: 15,
    marginLeft: 3,
  },
  adBannerWrap: {
    marginTop: 10,
    width: '100%',
    height: 200,
    backgroundColor: '#eee',
  },
  adBanner: {
    width: '100%',
    borderRadius: 12,
    borderWidth: 2,
    borderColor: 'red',
    backgroundColor: '#fff',
  },
  adDotsRow: {
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    marginTop: 8,
    marginBottom: 8,
  },
  adDot: {
    width: 8,
    height: 8,
    borderRadius: 4,
    backgroundColor: '#ccc',
    marginHorizontal: 3,
  },
  adDotActive: {
    backgroundColor: '#3FE0E8',
  },
  filterRow: {
    flexDirection: 'row',
    paddingHorizontal: 8,
    marginBottom: 10,
  },
  filterBtn: {
    paddingHorizontal: 14,
    paddingVertical: 7,
    borderRadius: 16,
    backgroundColor: '#f4f7fb',
    marginRight: 8,
    borderWidth: 1,
    borderColor: '#e0e0e0',
  },
  filterBtnActive: {
    backgroundColor: '#3FE0E8',
    borderColor: '#3FE0E8',
  },
  filterText: {
    color: '#888',
    fontSize: 15,
  },
  filterTextActive: {
    color: '#fff',
    fontWeight: 'bold',
  },
  productGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    justifyContent: 'space-between',
    paddingHorizontal: 12,
    marginTop: 8,
  },
  productCard: {
    backgroundColor: '#fff',
    borderRadius: 16, // was 12
    padding: 20, // was 12
    marginBottom: 20, // was 16
    alignItems: 'center',
    elevation: 3, // was 2
    shadowColor: '#3578C9',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.10, // was 0.08
    shadowRadius: 6, // was 4
    borderWidth: 1,
    borderColor: '#e0e0e0',
    minWidth: 150, // add min width for bigger card
    minHeight: 140, // add min height for bigger card
  },
  productImg: {
    width: 70,
    height: 70,
    marginBottom: 8,
  },
  productShop: {
    fontWeight: 'bold',
    fontSize: 15,
    marginBottom: 2,
    color: '#3578C9',
  },
  productDesc: {
    fontSize: 16, // was 13.5
    color: '#222',
    marginBottom: 4, // was 1
    fontWeight: '500', // add for better visibility
  },
  shopSection: {
    marginBottom: 24,
  },
  shopLogo: {
    width: CARD_WIDTH - 40,
    height: CARD_WIDTH - 40,
    borderRadius: 12,
    backgroundColor: '#eaf6fa',
    marginBottom: 10,
  },
  shopCard: {
    backgroundColor: '#fff',
    borderRadius: 16,
    padding: 18,
    marginBottom: CARD_MARGIN,
    width: CARD_WIDTH,
    alignItems: 'center',
    elevation: 3,
    shadowColor: '#3578C9',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.10,
    shadowRadius: 6,
  },
  shopName: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#3578C9',
    textAlign: 'center',
    marginTop: 4,
  },
  containerType: {
    fontSize: 14,
    color: '#888',
    textAlign: 'center',
    marginTop: 2,
  },
  gridContainer: {
    padding: CARD_MARGIN,
    paddingBottom: 24,
  },
  messageBtn: {
    marginTop: 10,
    paddingVertical: 8,
    paddingHorizontal: 15,
    backgroundColor: '#3FE0E8',
    borderRadius: 10,
    borderWidth: 1,
    borderColor: '#3FE0E8',
  },
  messageBtnText: {
    color: '#fff',
    fontSize: 14,
    fontWeight: 'bold',
  },
  modalBg: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: 'rgba(0,0,0,0.5)',
  },
  modalCard: {
    width: '90%',
    backgroundColor: '#fff',
    borderRadius: 15,
    padding: 20,
    alignItems: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.2,
    shadowRadius: 8,
  },
  modalTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    marginBottom: 15,
    color: '#333',
  },
  logContainer: {
    maxHeight: 300, // Limit the height of the message log
    width: '100%',
    marginBottom: 15,
  },
  logMsg: {
    maxWidth: '80%',
    padding: 10,
    borderRadius: 10,
    marginBottom: 8,
  },
  logMsgLeft: {
    backgroundColor: '#e0e0e0',
    alignSelf: 'flex-start',
  },
  logMsgRight: {
    backgroundColor: '#3FE0E8',
    alignSelf: 'flex-end',
  },
  logTime: {
    fontSize: 12,
    color: '#888',
    marginTop: 5,
    alignSelf: 'flex-end',
  },
  inputRow: {
    flexDirection: 'row',
    alignItems: 'center',
    width: '100%',
    marginTop: 10,
  },
  input: {
    flex: 1,
    backgroundColor: '#f4f7fb',
    borderRadius: 20,
    paddingVertical: 10,
    paddingHorizontal: 15,
    fontSize: 16,
    color: '#222',
    borderWidth: 1,
    borderColor: '#e0e0e0',
    marginRight: 10,
  },
}); 