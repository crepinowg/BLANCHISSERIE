const path = require('path');

module.exports = {
  mode: "development",
  entry: './templates/forms-wizard.html.twig',
  output: {
    filename: 'bundle.js',
    path: path.resolve(__dirname, 'dist'),
    
  },
  module: {
    rules: [
      {
        test: /\.twig$/,
        use: [
          {
            loader: 'twig-loader',
            options: {
              // Add options here if needed
            }
          }
        ]
      }
    ]
  },
  resolve: {
    fallback: {
      path: require.resolve("path-browserify")
    }
  }
  
  
};
