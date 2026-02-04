// tailwind.config.js
module.exports = {
  content: [
    // Inclua TODOS os caminhos possíveis
    "./public/**/*.php",
    "./public/*.php", // Adicione esta linha
    "./dashboard/**/*.php",
    "./dashboard/**/**/*.php",
    "./dashboard/**/**/**/*.php",
    "./dashboard/*.php", // Adicione esta linha
  ],
  theme: {
    extend: {
      colors: {
        primary: '#3b43ce',
        darktext: '#303951',
      },
      fontFamily: {
        heading: ['Bebas Neue', 'sans-serif'],
        body: ['Inter', 'system-ui', 'sans-serif'],
      },
    },
  },
  plugins: [],
}