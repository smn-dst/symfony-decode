describe('Smoke E2E', () => {
  it('la page d\'health répond', () => {
    cy.visit('/health');
    cy.url().should('include', '127.0.0.1:8000');
  });
});
