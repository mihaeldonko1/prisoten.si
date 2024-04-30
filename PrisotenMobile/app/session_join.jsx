import React, { useState } from 'react';
import { View, Text, TextInput, Button } from 'react-native';
import { useLocalSearchParams, useRouter } from 'expo-router';

function session_join() {
  const { user, tokens } = useLocalSearchParams();
  const tokensObj = JSON.parse(tokens);
  const userObj = JSON.parse(user);

  const [inputValue, setInputValue] = useState('');

  const handleJoinClick = () => {
    //TODO join v sejo, ki bo napisana v Laravelu
    console.log(inputValue);
  };

  return (
    <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
      <Text>Dobrodošli, {userObj.name}</Text>
      <TextInput
        style={{ height: 40, borderColor: 'gray', borderWidth: 1, marginTop: 20, width: 200, paddingHorizontal: 10 }}
        onChangeText={setInputValue}
        value={inputValue}
        placeholder="Vnesite kodo za prisotnost"
      />
      <Button
        title="Potrdi"
        onPress={handleJoinClick}
      />
    </View>
  );
}

export default session_join;
