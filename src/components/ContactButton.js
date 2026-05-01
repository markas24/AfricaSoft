import React from 'react';
import { TouchableOpacity, Text, View } from 'react-native';
import globalStyles from '../styles/globalStyles';

const ContactButton = ({ onPress, iconSymbol, title, subtitle, backgroundColor }) => {
  return (
    <TouchableOpacity 
      style={[globalStyles.contactButton, { backgroundColor }]} 
      onPress={onPress}
      activeOpacity={0.8}
    >
      <View style={globalStyles.contactIcon}>
        <Text style={globalStyles.contactIconText}>{iconSymbol}</Text>
      </View>
      <View style={globalStyles.contactTextContainer}>
        <Text style={globalStyles.contactTitle}>{title}</Text>
        <Text style={globalStyles.contactSubtitle}>{subtitle}</Text>
      </View>
    </TouchableOpacity>
  );
};

export default ContactButton;