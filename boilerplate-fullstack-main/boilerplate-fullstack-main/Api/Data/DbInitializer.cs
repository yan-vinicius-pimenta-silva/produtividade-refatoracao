using Api.Helpers;
using Api.Models;
using Microsoft.EntityFrameworkCore;

namespace Api.Data
{
    public static class DbInitializer
    {
        public static async Task SeedUsersAsync(ApiDbContext context)
        {
            var runSeed = Environment.GetEnvironmentVariable("RUN_USERS_SEED");
            if (!string.Equals(runSeed, "true", StringComparison.OrdinalIgnoreCase))
                return;

            if (await context.Users.AnyAsync())
                return;

            var users = new List<User>
            {
                new User { Username = "alice", Email = "alice@test.com", Password = "123456", FullName = "Alice Wonderland" },
                new User { Username = "bob", Email = "bob@test.com", Password = "123456", FullName = "Bob Builder" },
                new User { Username = "carol", Email = "carol@test.com", Password = "123456", FullName = "Carol Singer" },
                new User { Username = "dave", Email = "dave@test.com", Password = "123456", FullName = "Dave Grohl" },
                new User { Username = "eve", Email = "eve@test.com", Password = "123456", FullName = "Eve Online" },
                new User { Username = "frank", Email = "frank@test.com", Password = "123456", FullName = "Frank Ocean" },
                new User { Username = "grace", Email = "grace@test.com", Password = "123456", FullName = "Grace Hopper" },
                new User { Username = "heidi", Email = "heidi@test.com", Password = "123456", FullName = "Heidi Klum" },
                new User { Username = "ivan", Email = "ivan@test.com", Password = "123456", FullName = "Ivan Drago" },
                new User { Username = "judy", Email = "judy@test.com", Password = "123456", FullName = "Judy Hopps" },
            };

            foreach (var user in users)
            {
                user.Password = PasswordHashing.Generate(user.Password);
                user.CreatedAt = DateTime.UtcNow;
                user.UpdatedAt = DateTime.UtcNow;
            }

            await context.Users.AddRangeAsync(users);
            await context.SaveChangesAsync();

            Console.WriteLine("Seed de usuários de teste executada.");
        }

        public static async Task SeedSystemResourcesAsync(ApiDbContext context)
        {
            var resources = new List<SystemResource>
            {
                new SystemResource { Name = "root", ExhibitionName = "Administrador" },
                new SystemResource { Name = "users", ExhibitionName = "Gerenciamento de Usuários" },
                new SystemResource { Name = "resources", ExhibitionName = "Recursos do Sistema" },
                new SystemResource { Name = "reports", ExhibitionName = "Auditoria do Sistema" },
                new SystemResource { Name = "companies", ExhibitionName = "Empresas" },
                new SystemResource { Name = "activity-types", ExhibitionName = "Tipos de Atividade" },
                new SystemResource { Name = "activities", ExhibitionName = "Atividades" },
                new SystemResource { Name = "ufesp-rates", ExhibitionName = "Tabela UFESP" },
                new SystemResource { Name = "fiscal-activities", ExhibitionName = "Atividades Fiscais" },
                new SystemResource { Name = "service-orders", ExhibitionName = "Ordens de Serviço" },
            };

            var existing = await context.SystemResources
                .Select(r => r.Name)
                .ToListAsync();

            var now = DateTime.UtcNow;
            var newResources = resources
                .Where(resource => !existing.Contains(resource.Name))
                .Select(resource => new SystemResource
                {
                    Name = resource.Name,
                    ExhibitionName = resource.ExhibitionName,
                    CreatedAt = now,
                    UpdatedAt = now
                })
                .ToList();

            if (newResources.Count == 0)
            {
                Console.WriteLine("System resources já estão atualizados.");
                return;
            }

            await context.SystemResources.AddRangeAsync(newResources);
            await context.SaveChangesAsync();

            Console.WriteLine("Seed de system resources executada.");
        }

        public static async Task CreateRootAsync(ApiDbContext context)
        {
            if (await context.Users.AnyAsync(u => u.Username == "root"))
                return;

            var rootResource = await context.SystemResources.FirstOrDefaultAsync(r => r.Name == "root");
            if (rootResource == null)
            {
                rootResource = new SystemResource
                {
                    Name = "root",
                    ExhibitionName = "Administrador",
                    CreatedAt = DateTime.UtcNow,
                    UpdatedAt = DateTime.UtcNow
                };
                await context.SystemResources.AddAsync(rootResource);
                await context.SaveChangesAsync();
            }

            var rootUser = new User
            {
                Username = "root",
                Email = "root@admin.com",
                Password = PasswordHashing.Generate("root1234"), // trocar para senha segura em produção
                FullName = "Administrador",
                Active = true,
                CreatedAt = DateTime.UtcNow,
                UpdatedAt = DateTime.UtcNow
            };

            await context.Users.AddAsync(rootUser);
            await context.SaveChangesAsync();

            var accessPermission = new AccessPermission
            {
                UserId = rootUser.Id,
                SystemResourceId = rootResource.Id,
                CreatedAt = DateTime.UtcNow,
                UpdatedAt = DateTime.UtcNow
            };

            await context.AccessPermissions.AddAsync(accessPermission);
            await context.SaveChangesAsync();

            Console.WriteLine("Usuário root criado com sucesso.");
        }

        public static async Task SeedAllAsync(ApiDbContext context)
        {
            await SeedUsersAsync(context);
            await SeedSystemResourcesAsync(context);
            await CreateRootAsync(context);
        }
    }
}
