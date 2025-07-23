document.addEventListener('DOMContentLoaded', function () {
  const el = document.getElementById('chat-app');
  if (!el || typeof Vue === 'undefined') return;

  const { createApp } = Vue;

  createApp({
    data() {
      return {
        messages: [],
        newMessage: '',
        aiTyping: false
      };
    },
    created() {
      this.loadMessages();
    },
    methods: {
      loadMessages() {
        fetch('/wp-json/custom/v1/messages')
          .then(r => r.json())
          .then(d => { this.messages = Array.isArray(d) ? d : []; });
      },
      send() {
        const text = this.newMessage.trim();
        if (!text) return;
        this.messages.push({ content: text, from: 'me' });
        this.newMessage = '';
        this.aiTyping = true;
        fetch('/wp-json/custom/v1/message', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ message: text })
        }).catch(() => {});
        fetch('/wp-json/custom/v1/ai-chat', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ message: text })
        })
          .then(r => r.json())
          .then(res => {
            if (res && res.type === 'exclusive_message' && res.link) {
              this.messages.push({ content: res.link, from: 'ai', exclusive: true });
            } else if (res && res.reply) {
              this.messages.push({ content: res.reply, from: 'ai' });
            }
          })
          .finally(() => {
            this.aiTyping = false;
          });
      }
    },
    template: `
      <div class="chat-box">
        <div class="messages">
          <div v-for="(m, i) in messages" :key="i" :class="['chat-message', m.from]">
            <span v-if="!m.exclusive" v-html="m.content"></span>
            <div v-else class="payment-prompt">
              <a :href="m.content">Unlock Exclusive Message</a>
            </div>
          </div>
          <div v-if="aiTyping" class="typing">AI is typing...</div>
        </div>
        <div class="input-area">
          <input v-model="newMessage" @keyup.enter="send" placeholder="Type a message" />
          <button @click="send">Send</button>
        </div>
      </div>`
  }).mount('#chat-app');
});
