import React from 'react';
import { View, Text } from 'react-native';
import globalStyles from '../styles/globalStyles';

const ServiceCard = ({ icon, name, description, color }) => {
  const getIconSymbol = () => {
    switch(icon) {
      case 'web':
        return '🌐';
      case 'android':
        return '📱';
      case 'ussd':
        return '📡';
      default:
        return '⚙️';
    }
  };

  return (
    <View style={globalStyles.serviceCard}>
      <View style={[globalStyles.serviceIconContainer, { backgroundColor: color }]}>
        <Text style={globalStyles.serviceIconText}>{getIconSymbol()}</Text>
      </View>
      <View style={globalStyles.serviceContent}>
        <Text style={globalStyles.serviceTitle}>{name}</Text>
        <Text style={globalStyles.serviceDescription}>{description}</Text>
      </View>
    </View>
  );
};

export default ServiceCard;