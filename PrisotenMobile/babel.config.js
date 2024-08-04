module.exports = function(api) {
  api.cache(true);
  return {
    presets: ['babel-preset-expo'],
    plugins: [
      '@babel/plugin-transform-react-jsx', // Add this plugin if it's not already included by babel-preset-expo
    ],
  };
};
