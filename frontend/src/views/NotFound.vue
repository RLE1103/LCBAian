<template>
  <div class="not-found">
    <h1>404</h1>
    <p>Oops! The page you are looking for does not exist.</p>
    <router-link to="/">Go back home</router-link>
  </div>
</template>

<script>
export default {

};
</script>

<style scoped>
.not-found {
  display: flex;
  flex-direction: column;
  justify-content: center; /* vertical center */
  align-items: center;     /* horizontal center */
  height: 100%;           /* full viewport height */
  text-align: center;
  background-color: #f8f8f8; /* optional, looks cleaner */
}

.not-found h1 {
  font-size: 6rem;
  color: #ff4c4c;
  margin: 0;
}

.not-found p {
  font-size: 1.5rem;
  margin: 10px 0 20px 0;
}

.not-found a {
  color: #0044ff;
  text-decoration: none;
  font-weight: bold;
  padding: 10px 20px;
  border: 2px solid #0011ff;
  border-radius: 5px;
  transition: background-color 0.3s, color 0.3s;
}

.not-found a:hover {
  background-color: #1900ff;
  color: white;
}
</style>
