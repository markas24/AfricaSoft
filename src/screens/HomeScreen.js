import React from 'react';
import {
  View,
  Text,
  ScrollView,
  Linking,
} from 'react-native';
import Header from '../components/Header';
import ServiceCard from '../components/ServiceCard';
import ContactButton from '../components/ContactButton';
import globalStyles from '../styles/globalStyles';

const HomeScreen = () => {
  const handleWhatsApp = () => {
    const phoneNumber = '+243854507945';
    const url = `https://wa.me/${phoneNumber.replace('+', '')}`;
    Linking.openURL(url);
  };

  const handleEmail = () => {
    Linking.openURL('mailto:contact@africasoft.dev');
  };

  const services = [
    {
      icon: 'web',
      name: 'Web Development',
      description: 'Sites web modernes et applications web progressives',
      color: '#4A90E2'
    },
    {
      icon: 'android',
      name: 'Android Development',
      description: 'Applications mobiles natives performantes',
      color: '#3DDC84'
    },
    {
      icon: 'ussd',
      name: 'USSD Development',
      description: 'Solutions USSD innovantes pour l\'Afrique',
      color: '#F39C12'
    }
  ];

  return (
    <ScrollView style={globalStyles.container} showsVerticalScrollIndicator={false}>
      <Header />
      
      <View style={globalStyles.content}>
        <View style={globalStyles.descriptionSection}>
          <Text style={globalStyles.sectionTitle}>Qui sommes-nous ?</Text>
          <Text style={globalStyles.description}>
            AfricaSoftt DEV est une entreprise leader dans le développement de solutions digitales innovantes. 
            Spécialisés dans la création d'applications web, mobiles Android et services USSD, 
            nous accompagnons les entreprises africaines dans leur transformation numérique.
          </Text>
        </View>

        <View style={globalStyles.servicesSection}>
          <Text style={globalStyles.sectionTitle}>Nos Services</Text>
          {services.map((service, index) => (
            <ServiceCard key={index} {...service} />
          ))}
        </View>

        <View style={globalStyles.contactSection}>
          <Text style={globalStyles.sectionTitle}>Contactez-nous</Text>
          
          <ContactButton
            onPress={handleWhatsApp}
            iconSymbol="💬"
            title="WhatsApp"
            subtitle="+243 854 507 945"
            backgroundColor="#25D366"
          />
          
          <ContactButton
            onPress={handleEmail}
            iconSymbol="✉️"
            title="Email"
            subtitle="contact@africasoft.dev"
            backgroundColor="#D44638"
          />
        </View>

        <View style={globalStyles.footer}>
          <Text style={globalStyles.footerText}>
            © 2024 AfricaSoftt DEV - Tous droits réservés
          </Text>
        </View>
      </View>
    </ScrollView>
  );
};

export default HomeScreen;