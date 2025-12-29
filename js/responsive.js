// Responsive behavior enhancements
document.addEventListener('DOMContentLoaded', function() {
  // Adjust viewport height for mobile browsers
  const vh = window.innerHeight * 0.01;
  document.documentElement.style.setProperty('--vh', `${vh}px`);
  
  window.addEventListener('resize', () => {
    const vh = window.innerHeight * 0.01;
    document.documentElement.style.setProperty('--vh', `${vh}px`);
  });
  
  // Improve form field focus on mobile
  // const formFields = document.querySelectorAll('input, select');
  // formFields.forEach(field => {
  //   field.addEventListener('focus', function() {
  //     // Scroll to field with offset for better visibility
  //     setTimeout(() => {
  //       const rect = this.getBoundingClientRect();
  //       const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
  //       const targetY = rect.top + scrollTop - 100;
  //       window.scrollTo({top: targetY, behavior: 'smooth'});
  //     }, 300);
  //   });
  // });
});