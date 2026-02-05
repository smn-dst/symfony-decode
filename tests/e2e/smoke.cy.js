describe('Smoke E2E', () => {
  it('la page d\'health répond', () => {
    cy.visit('/health');
    cy.url().should('include', '/health');
  });
});
