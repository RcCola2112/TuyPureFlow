import React from 'react';
import { TouchableOpacity, Text } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { FontAwesome5 } from '@expo/vector-icons';

const ConsumerNavBar = () => {
  const navigation = useNavigation();

  return (
    <TouchableOpacity style={styles.iconBtn} onPress={() => navigation.navigate('CartCheckout')}>
      <FontAwesome5 name="shopping-cart" size={18} color="#3FE0E8" />
      <Text style={styles.iconLabel}>Cart</Text>
    </TouchableOpacity>
  );
};

const styles = {
  iconBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 10,
  },
  iconLabel: {
    marginLeft: 10,
    fontSize: 16,
    fontWeight: 'bold',
  },
};

export default ConsumerNavBar; 