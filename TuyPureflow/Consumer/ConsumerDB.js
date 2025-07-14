import React, { useState, useRef, useEffect } from 'react';
import { View, Text, Image, StyleSheet, ScrollView, TouchableOpacity, TextInput, useWindowDimensions, FlatList, Platform } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { Ionicons, MaterialIcons, FontAwesome, FontAwesome5, Entypo } from '@expo/vector-icons';
import ConsumerNavBar from './ConsumerNavBar';

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

export default function ConsumerDB() {
  const navigation = useNavigation();
  const { width } = useWindowDimensions();
  const isTablet = width > 600;
  const CARD_WIDTH = width > 400 ? 170 : (width - 48) / 2;
  const BANNER_HEIGHT = 200;
  const [selectedFilter, setSelectedFilter] = useState('All');
  const [search, setSearch] = useState('');
  const [adIndex, setAdIndex] = useState(1);
  const scrollRef = useRef();
  const [language, setLanguage] = useState('English');

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

  return (
    <View style={{ flex: 1, backgroundColor: '#fff' }}>
      <ConsumerNavBar navigation={navigation} search={search} setSearch={setSearch} style={styles.fixedNavBar} />
      <View style={{ flex: 1 }}>
        <ScrollView contentContainerStyle={{ paddingBottom: 24 }} showsVerticalScrollIndicator={false}>
          {/* Advertisement Banner - Modern Full-Width Carousel */}
          <ScrollView
            horizontal
            pagingEnabled
            showsHorizontalScrollIndicator={false}
            ref={scrollRef}
            style={{ width, height: BANNER_HEIGHT, backgroundColor: '#fff', marginTop: 10 }}
            onMomentumScrollEnd={handleMomentumScrollEnd}
          >
            {carouselAds.map((img, idx) => (
              <Image
                key={idx}
                source={img}
                style={{
                  width,
                  height: BANNER_HEIGHT,
                  resizeMode: 'cover',
                  borderRadius: 12,
                }}
              />
            ))}
          </ScrollView>
          <View style={styles.adDotsRow}>
            {adImages.map((_, idx) => (
              <View key={idx} style={[styles.adDot, adIndex - 1 === idx && styles.adDotActive]} />
            ))}
          </View>
          {/* Filters */}
          <ScrollView horizontal showsHorizontalScrollIndicator={false} style={styles.filterRow}>
            {filters.map((f) => (
              <TouchableOpacity
                key={f}
                style={[styles.filterBtn, selectedFilter === f && styles.filterBtnActive]}
                onPress={() => setSelectedFilter(f)}
              >
                <Text style={[styles.filterText, selectedFilter === f && styles.filterTextActive]}>{f}</Text>
              </TouchableOpacity>
            ))}
          </ScrollView>
          {/* Product Grid */}
          <View style={styles.productGrid}>
            {products.map((item) => (
              <TouchableOpacity
                key={item.id}
                style={[styles.productCard, { width: CARD_WIDTH }]}
                onPress={() => navigation.navigate('ShopDetail', { product: item })}
              >
                <Image source={item.image} style={styles.productImg} resizeMode="contain" />
                <Text style={styles.productShop}>{item.shop}</Text>
                <Text style={styles.productDesc}>{item.container}:</Text>
                <Text style={styles.productDesc}>Refill: {item.refill}</Text>
                <Text style={styles.productDesc}>Retail: {item.retail}</Text>
                <Text style={styles.productDesc}>Special delivery: {item.special}</Text>
              </TouchableOpacity>
            ))}
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
    borderRadius: 12,
    padding: 12,
    marginBottom: 16,
    alignItems: 'center',
    elevation: 2,
    shadowColor: '#3578C9',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.08,
    shadowRadius: 4,
    borderWidth: 1,
    borderColor: '#e0e0e0',
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
    fontSize: 13.5,
    color: '#222',
    marginBottom: 1,
  },
}); 