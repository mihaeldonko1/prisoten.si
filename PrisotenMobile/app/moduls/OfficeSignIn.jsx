import React, { useEffect, useState } from 'react';
import { View, Button, Text } from 'react-native';
import * as WebBrowser from 'expo-web-browser';
import * as AuthSession from "expo-auth-session";
import { router } from 'expo-router';
import Styles from './Styles';

const tenetID = "a4d626db-4464-4084-a8cc-552ef72031b9";
const clientID = "f6f28b05-e13c-40ee-bcd8-dcb8631650b7";

WebBrowser.maybeCompleteAuthSession();

export default function OfficeSignIn() {
    const [discovery, setDiscovery] = useState({});
    const [authRequest, setAuthRequest] = useState({});
    const [authorizeResult, setAuthorizeResult] = useState({});
    const [errorMessage, setErrorMessage] = useState('');

    const scopes = ['openid', 'profile', 'email', 'offline_access'];
    const domain = `https://login.microsoftonline.com/${tenetID}/v2.0`;
    const redirectUrl = AuthSession.makeRedirectUri(__DEV__ ? { scheme: 'prisotenOnline' } : {});

    useEffect(() => {
        const getSession = async () => {
            const d = await AuthSession.fetchDiscoveryAsync(domain);
            const authRequestOptions = {
                prompt: AuthSession.Prompt.Login,
                responseType: AuthSession.ResponseType.Code,
                scopes: scopes,
                usePKCE: true,
                clientId: clientID,
                redirectUri: __DEV__ ? redirectUrl : redirectUrl + "example",
            };
            const authRequest = new AuthSession.AuthRequest(authRequestOptions);
            setAuthRequest(authRequest);
            setDiscovery(d);
        };
        getSession();
    }, []);

    useEffect(() => {
        const getCodeExchange = async () => {
            try {
                const tokenResult = await AuthSession.exchangeCodeAsync(
                    {
                        code: authorizeResult.params.code,
                        clientId: clientID,
                        redirectUri: __DEV__ ? redirectUrl : redirectUrl + "example",
                        extraParams: {
                            code_verifier: authRequest.codeVerifier || "",
                        },
                    },
                    discovery
                );
                if (tokenResult) {
                    const response = await fetch(`https://graph.microsoft.com/oidc/userinfo`, {
                        headers: {
                            Authorization: `Bearer ${tokenResult.accessToken}`,
                        },
                    });
                    const userInfo = await response.json();

                    router.push({
                        pathname: '/moduls/Session_join',
                        params: {
                            user: JSON.stringify(userInfo),
                            tokens: JSON.stringify(tokenResult),
                        },
                    });
                }
            } catch (error) {
                console.error('Napaka pri pridobivanju podatkov:', error);
                setErrorMessage('Napaka pri prijavi, poskusite z prijavo ponovno.');
            }
        };

        if (authorizeResult && authorizeResult.type === 'error') {
            setErrorMessage('Napaka pri avtentikaciji, poskusite z prijavo ponovno.');
        } else if (authorizeResult && authorizeResult.type === 'success' && authRequest && authRequest.codeVerifier) {
            getCodeExchange();
        }
    }, [authorizeResult, authRequest]);

    return (
        <View style={Styles.container}>
            {
                authRequest && discovery ? (
                    <>
                        <Button
                            title="Prijava z študentsko identiteto"
                            accessibilityLabel="Prijava z študentsko identiteto"
                            onPress={async () => {
                                const result = await authRequest.promptAsync(discovery);
                                setAuthorizeResult(result);
                            }}
                        />
                        {errorMessage ? <Text style={Styles.errorText}>{errorMessage}</Text> : null}
                    </>
                ) : null
            }
        </View>
    );
}