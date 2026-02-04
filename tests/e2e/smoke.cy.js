describe('Smoke E2E', () => {
  it('la page d\'accueil répond', () => {
    cy.visit('/home');
    cy.url().should('include', '127.0.0.1:8000');
  });
});
