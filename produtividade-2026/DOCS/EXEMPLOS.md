# Exemplos de Código

Exemplos práticos de código para tarefas comuns.

## Índice

1. [Backend - Services](#backend---services)
2. [Backend - Controllers](#backend---controllers)
3. [Frontend - Hooks](#frontend---hooks)
4. [Frontend - Componentes](#frontend---componentes)
5. [Integrações](#integrações)

## Backend - Services

### Criar Entidade com Validação

```csharp
using Api.Data;
using Api.Dtos.Products;
using Api.Models;
using Api.Repositories;
using Microsoft.EntityFrameworkCore;

namespace Api.Services.ProductsServices;

public class CreateProduct
{
    private readonly IGenericRepository<Product> _productRepository;
    private readonly ApiDbContext _context;
    private readonly CreateSystemLog _logService;

    public CreateProduct(
        IGenericRepository<Product> productRepository,
        ApiDbContext context,
        CreateSystemLog logService)
    {
        _productRepository = productRepository;
        _context = context;
        _logService = logService;
    }

    public async Task<ProductResponseDto> ExecuteAsync(CreateProductDto dto, int authUserId)
    {
        // Validação: nome único
        var existingProduct = await _context.Products
            .FirstOrDefaultAsync(p => p.Name == dto.Name && p.Active);

        if (existingProduct != null)
        {
            throw new BadRequestException("Produto com este nome já existe");
        }

        // Validação: preço positivo
        if (dto.Price <= 0)
        {
            throw new BadRequestException("Preço deve ser maior que zero");
        }

        // Criar entidade
        var product = new Product
        {
            Name = dto.Name,
            Description = dto.Description,
            Price = dto.Price,
            Stock = dto.Stock,
            CreatedAt = DateTime.UtcNow,
            UpdatedAt = DateTime.UtcNow
        };

        // Salvar
        var created = await _productRepository.CreateAsync(product);

        // Log de auditoria
        await _logService.ExecuteAsync(authUserId, $"Produto criado: {created.Name}");

        // Retornar DTO
        return new ProductResponseDto
        {
            Id = created.Id,
            Name = created.Name,
            Description = created.Description,
            Price = created.Price,
            Stock = created.Stock,
            Active = created.Active,
            CreatedAt = created.CreatedAt,
            UpdatedAt = created.UpdatedAt
        };
    }
}
```

### Buscar com Filtros e Paginação

```csharp
using Api.Data;
using Api.Dtos.Products;
using Microsoft.EntityFrameworkCore;

namespace Api.Services.ProductsServices;

public class GetProductsFiltered
{
    private readonly ApiDbContext _context;

    public GetProductsFiltered(ApiDbContext context)
    {
        _context = context;
    }

    public async Task<object> ExecuteAsync(
        int page,
        int limit,
        string? name = null,
        decimal? minPrice = null,
        decimal? maxPrice = null,
        bool? inStock = null)
    {
        var query = _context.Products
            .Where(p => p.Active)
            .AsQueryable();

        // Filtro por nome
        if (!string.IsNullOrEmpty(name))
        {
            query = query.Where(p => p.Name.Contains(name));
        }

        // Filtro por preço mínimo
        if (minPrice.HasValue)
        {
            query = query.Where(p => p.Price >= minPrice.Value);
        }

        // Filtro por preço máximo
        if (maxPrice.HasValue)
        {
            query = query.Where(p => p.Price <= maxPrice.Value);
        }

        // Filtro por estoque
        if (inStock.HasValue)
        {
            query = inStock.Value
                ? query.Where(p => p.Stock > 0)
                : query.Where(p => p.Stock == 0);
        }

        // Total antes da paginação
        var total = await query.CountAsync();

        // Paginação
        var products = await query
            .OrderByDescending(p => p.CreatedAt)
            .Skip((page - 1) * limit)
            .Take(limit)
            .Select(p => new ProductResponseDto
            {
                Id = p.Id,
                Name = p.Name,
                Description = p.Description,
                Price = p.Price,
                Stock = p.Stock,
                Active = p.Active,
                CreatedAt = p.CreatedAt,
                UpdatedAt = p.UpdatedAt
            })
            .ToListAsync();

        return new
        {
            Data = products,
            Total = total,
            Page = page,
            Limit = limit
        };
    }
}
```

### Atualizar com Campos Opcionais

```csharp
using Api.Repositories;
using Api.Models;
using Api.Dtos.Products;
using Microsoft.EntityFrameworkCore;

namespace Api.Services.ProductsServices;

public class UpdateProduct
{
    private readonly IGenericRepository<Product> _repository;
    private readonly ApiDbContext _context;
    private readonly CreateSystemLog _logService;

    public UpdateProduct(
        IGenericRepository<Product> repository,
        ApiDbContext context,
        CreateSystemLog logService)
    {
        _repository = repository;
        _context = context;
        _logService = logService;
    }

    public async Task<ProductResponseDto> ExecuteAsync(
        int id,
        UpdateProductDto dto,
        int authUserId)
    {
        // Buscar produto
        var product = await _repository.GetByIdAsync(id);
        if (product == null)
        {
            throw new NotFoundException("Produto não encontrado");
        }

        // Validar nome único (se alterado)
        if (!string.IsNullOrEmpty(dto.Name) && dto.Name != product.Name)
        {
            var existingProduct = await _context.Products
                .FirstOrDefaultAsync(p => p.Name == dto.Name && p.Active && p.Id != id);

            if (existingProduct != null)
            {
                throw new BadRequestException("Produto com este nome já existe");
            }
        }

        // Atualizar campos (apenas se fornecidos)
        if (!string.IsNullOrEmpty(dto.Name))
        {
            product.Name = dto.Name;
        }

        if (!string.IsNullOrEmpty(dto.Description))
        {
            product.Description = dto.Description;
        }

        if (dto.Price.HasValue)
        {
            if (dto.Price.Value <= 0)
            {
                throw new BadRequestException("Preço deve ser maior que zero");
            }
            product.Price = dto.Price.Value;
        }

        if (dto.Stock.HasValue)
        {
            if (dto.Stock.Value < 0)
            {
                throw new BadRequestException("Estoque não pode ser negativo");
            }
            product.Stock = dto.Stock.Value;
        }

        product.UpdatedAt = DateTime.UtcNow;

        // Salvar
        var updated = await _repository.UpdateAsync(product);

        // Log
        await _logService.ExecuteAsync(authUserId, $"Produto atualizado: {updated.Name}");

        // Retornar
        return new ProductResponseDto
        {
            Id = updated.Id,
            Name = updated.Name,
            Description = updated.Description,
            Price = updated.Price,
            Stock = updated.Stock,
            Active = updated.Active,
            CreatedAt = updated.CreatedAt,
            UpdatedAt = updated.UpdatedAt
        };
    }
}
```

## Backend - Controllers

### Controller Completo com Todos os Endpoints

```csharp
using Microsoft.AspNetCore.Mvc;
using Api.Dtos.Products;
using Api.Services.ProductsServices;
using Api.Helpers;

namespace Api.Controllers;

[ApiController]
[Route("api/[controller]")]
public class ProductsController : ControllerBase
{
    private readonly CreateProduct _createProduct;
    private readonly GetAllProducts _getAllProducts;
    private readonly GetProductById _getProductById;
    private readonly GetProductsFiltered _getProductsFiltered;
    private readonly UpdateProduct _updateProduct;
    private readonly DeleteProduct _deleteProduct;
    private readonly SearchProducts _searchProducts;

    public ProductsController(
        CreateProduct createProduct,
        GetAllProducts getAllProducts,
        GetProductById getProductById,
        GetProductsFiltered getProductsFiltered,
        UpdateProduct updateProduct,
        DeleteProduct deleteProduct,
        SearchProducts searchProducts)
    {
        _createProduct = createProduct;
        _getAllProducts = getAllProducts;
        _getProductById = getProductById;
        _getProductsFiltered = getProductsFiltered;
        _updateProduct = updateProduct;
        _deleteProduct = deleteProduct;
        _searchProducts = searchProducts;
    }

    /// <summary>
    /// Lista todos os produtos com paginação
    /// </summary>
    [HttpGet]
    public async Task<ActionResult> GetAll(
        [FromQuery] int page = 1,
        [FromQuery] int limit = 10)
    {
        var result = await _getAllProducts.ExecuteAsync(page, limit);
        return Ok(result);
    }

    /// <summary>
    /// Busca produtos com filtros
    /// </summary>
    [HttpGet("filter")]
    public async Task<ActionResult> GetFiltered(
        [FromQuery] int page = 1,
        [FromQuery] int limit = 10,
        [FromQuery] string? name = null,
        [FromQuery] decimal? minPrice = null,
        [FromQuery] decimal? maxPrice = null,
        [FromQuery] bool? inStock = null)
    {
        var result = await _getProductsFiltered.ExecuteAsync(
            page, limit, name, minPrice, maxPrice, inStock);
        return Ok(result);
    }

    /// <summary>
    /// Busca produtos por texto
    /// </summary>
    [HttpGet("search")]
    public async Task<ActionResult> Search(
        [FromQuery] string key,
        [FromQuery] int page = 1,
        [FromQuery] int limit = 10)
    {
        var result = await _searchProducts.ExecuteAsync(key, page, limit);
        return Ok(result);
    }

    /// <summary>
    /// Busca produto por ID
    /// </summary>
    [HttpGet("{id}")]
    public async Task<ActionResult> GetById(int id)
    {
        var result = await _getProductById.ExecuteAsync(id);
        return Ok(result);
    }

    /// <summary>
    /// Cria novo produto
    /// </summary>
    [HttpPost]
    public async Task<ActionResult> Create([FromBody] CreateProductDto dto)
    {
        var authUserId = CurrentAuthUser.GetId(HttpContext);
        var result = await _createProduct.ExecuteAsync(dto, authUserId);
        return CreatedAtAction(nameof(GetById), new { id = result.Id }, result);
    }

    /// <summary>
    /// Atualiza produto existente
    /// </summary>
    [HttpPut("{id}")]
    public async Task<ActionResult> Update(int id, [FromBody] UpdateProductDto dto)
    {
        var authUserId = CurrentAuthUser.GetId(HttpContext);
        var result = await _updateProduct.ExecuteAsync(id, dto, authUserId);
        return Ok(result);
    }

    /// <summary>
    /// Deleta produto (soft delete)
    /// </summary>
    [HttpDelete("{id}")]
    public async Task<ActionResult> Delete(int id)
    {
        var authUserId = CurrentAuthUser.GetId(HttpContext);
        await _deleteProduct.ExecuteAsync(id, authUserId);
        return NoContent();
    }
}
```

## Frontend - Hooks

### Hook Customizado Completo

```typescript
import { useState, useCallback } from 'react';
import { Product, CreateProductDto, UpdateProductDto } from '../interfaces/Product';
import { productsServices } from '../services/productsServices';

interface UseProductsReturn {
  products: Product[];
  totalProducts: number;
  currentPage: number;
  loading: boolean;
  error: string | null;
  fetchProducts: (page: number, limit: number) => Promise<void>;
  fetchProductsFiltered: (filters: ProductFilters) => Promise<void>;
  searchProducts: (key: string, page: number, limit: number) => Promise<void>;
  createProduct: (data: CreateProductDto) => Promise<void>;
  updateProduct: (id: number, data: UpdateProductDto) => Promise<void>;
  deleteProduct: (id: number) => Promise<void>;
  clearError: () => void;
}

interface ProductFilters {
  page: number;
  limit: number;
  name?: string;
  minPrice?: number;
  maxPrice?: number;
  inStock?: boolean;
}

export const useProducts = (): UseProductsReturn => {
  const [products, setProducts] = useState<Product[]>([]);
  const [totalProducts, setTotalProducts] = useState(0);
  const [currentPage, setCurrentPage] = useState(1);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const clearError = useCallback(() => {
    setError(null);
  }, []);

  const fetchProducts = useCallback(async (page: number, limit: number) => {
    setLoading(true);
    setError(null);
    try {
      const response = await productsServices.listProducts(page, limit);
      setProducts(response.data);
      setTotalProducts(response.total);
      setCurrentPage(page);
    } catch (err: any) {
      setError(err.response?.data?.error || 'Erro ao buscar produtos');
      console.error('Error fetching products:', err);
    } finally {
      setLoading(false);
    }
  }, []);

  const fetchProductsFiltered = useCallback(async (filters: ProductFilters) => {
    setLoading(true);
    setError(null);
    try {
      const response = await productsServices.listProductsFiltered(filters);
      setProducts(response.data);
      setTotalProducts(response.total);
      setCurrentPage(filters.page);
    } catch (err: any) {
      setError(err.response?.data?.error || 'Erro ao buscar produtos');
      console.error('Error fetching filtered products:', err);
    } finally {
      setLoading(false);
    }
  }, []);

  const searchProducts = useCallback(async (key: string, page: number, limit: number) => {
    setLoading(true);
    setError(null);
    try {
      const response = await productsServices.searchProducts(key, page, limit);
      setProducts(response.data);
      setTotalProducts(response.total);
      setCurrentPage(page);
    } catch (err: any) {
      setError(err.response?.data?.error || 'Erro ao buscar produtos');
      console.error('Error searching products:', err);
    } finally {
      setLoading(false);
    }
  }, []);

  const createProduct = useCallback(async (data: CreateProductDto) => {
    setLoading(true);
    setError(null);
    try {
      await productsServices.createProduct(data);
      // Recarrega a lista atual
      await fetchProducts(currentPage, 10);
    } catch (err: any) {
      setError(err.response?.data?.error || 'Erro ao criar produto');
      console.error('Error creating product:', err);
      throw err;
    } finally {
      setLoading(false);
    }
  }, [currentPage, fetchProducts]);

  const updateProduct = useCallback(async (id: number, data: UpdateProductDto) => {
    setLoading(true);
    setError(null);
    try {
      await productsServices.updateProduct(id, data);
      await fetchProducts(currentPage, 10);
    } catch (err: any) {
      setError(err.response?.data?.error || 'Erro ao atualizar produto');
      console.error('Error updating product:', err);
      throw err;
    } finally {
      setLoading(false);
    }
  }, [currentPage, fetchProducts]);

  const deleteProduct = useCallback(async (id: number) => {
    setLoading(true);
    setError(null);
    try {
      await productsServices.deleteProduct(id);
      await fetchProducts(currentPage, 10);
    } catch (err: any) {
      setError(err.response?.data?.error || 'Erro ao deletar produto');
      console.error('Error deleting product:', err);
      throw err;
    } finally {
      setLoading(false);
    }
  }, [currentPage, fetchProducts]);

  return {
    products,
    totalProducts,
    currentPage,
    loading,
    error,
    fetchProducts,
    fetchProductsFiltered,
    searchProducts,
    createProduct,
    updateProduct,
    deleteProduct,
    clearError,
  };
};
```

## Frontend - Componentes

### Formulário com Validação

```typescript
import { useState } from 'react';
import {
  Box,
  TextField,
  Button,
  Typography,
  Alert,
} from '@mui/material';
import { CreateProductDto } from '../../interfaces/Product';

interface Props {
  onSubmit: (data: CreateProductDto) => Promise<void>;
  onCancel?: () => void;
}

const ProductForm = ({ onSubmit, onCancel }: Props) => {
  const [formData, setFormData] = useState<CreateProductDto>({
    name: '',
    description: '',
    price: 0,
    stock: 0,
  });
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [submitting, setSubmitting] = useState(false);
  const [submitError, setSubmitError] = useState<string | null>(null);

  const validate = (): boolean => {
    const newErrors: Record<string, string> = {};

    if (!formData.name.trim()) {
      newErrors.name = 'Nome é obrigatório';
    }

    if (formData.price <= 0) {
      newErrors.price = 'Preço deve ser maior que zero';
    }

    if (formData.stock < 0) {
      newErrors.stock = 'Estoque não pode ser negativo';
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setSubmitError(null);

    if (!validate()) {
      return;
    }

    setSubmitting(true);
    try {
      await onSubmit(formData);
      // Reset form
      setFormData({
        name: '',
        description: '',
        price: 0,
        stock: 0,
      });
    } catch (err: any) {
      setSubmitError(err.response?.data?.error || 'Erro ao salvar produto');
    } finally {
      setSubmitting(false);
    }
  };

  const handleChange = (field: keyof CreateProductDto) => (
    e: React.ChangeEvent<HTMLInputElement>
  ) => {
    const value = field === 'price' || field === 'stock'
      ? parseFloat(e.target.value) || 0
      : e.target.value;

    setFormData(prev => ({ ...prev, [field]: value }));
    // Limpa erro do campo
    if (errors[field]) {
      setErrors(prev => ({ ...prev, [field]: '' }));
    }
  };

  return (
    <Box component="form" onSubmit={handleSubmit} sx={{ mt: 2 }}>
      <Typography variant="h6" gutterBottom>
        Novo Produto
      </Typography>

      {submitError && (
        <Alert severity="error" sx={{ mb: 2 }}>
          {submitError}
        </Alert>
      )}

      <TextField
        fullWidth
        label="Nome"
        name="name"
        value={formData.name}
        onChange={handleChange('name')}
        error={Boolean(errors.name)}
        helperText={errors.name}
        margin="normal"
        required
      />

      <TextField
        fullWidth
        label="Descrição"
        name="description"
        value={formData.description}
        onChange={handleChange('description')}
        multiline
        rows={3}
        margin="normal"
      />

      <TextField
        fullWidth
        label="Preço"
        name="price"
        type="number"
        value={formData.price}
        onChange={handleChange('price')}
        error={Boolean(errors.price)}
        helperText={errors.price}
        margin="normal"
        required
        inputProps={{ min: 0, step: 0.01 }}
      />

      <TextField
        fullWidth
        label="Estoque"
        name="stock"
        type="number"
        value={formData.stock}
        onChange={handleChange('stock')}
        error={Boolean(errors.stock)}
        helperText={errors.stock}
        margin="normal"
        required
        inputProps={{ min: 0 }}
      />

      <Box sx={{ mt: 2, display: 'flex', gap: 2 }}>
        <Button
          type="submit"
          variant="contained"
          color="primary"
          disabled={submitting}
        >
          {submitting ? 'Salvando...' : 'Salvar'}
        </Button>

        {onCancel && (
          <Button
            variant="outlined"
            onClick={onCancel}
            disabled={submitting}
          >
            Cancelar
          </Button>
        )}
      </Box>
    </Box>
  );
};

export default ProductForm;
```

### Tabela com Ações e Paginação

```typescript
import {
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  Paper,
  IconButton,
  TablePagination,
  Chip,
} from '@mui/material';
import EditIcon from '@mui/icons-material/Edit';
import DeleteIcon from '@mui/icons-material/Delete';
import { Product } from '../../interfaces/Product';

interface Props {
  products: Product[];
  total: number;
  page: number;
  limit: number;
  loading: boolean;
  onEdit: (product: Product) => void;
  onDelete: (id: number) => void;
  onPageChange: (page: number, limit: number) => void;
}

const ProductsTable = ({
  products,
  total,
  page,
  limit,
  loading,
  onEdit,
  onDelete,
  onPageChange,
}: Props) => {
  const handleChangePage = (_: unknown, newPage: number) => {
    onPageChange(newPage + 1, limit);
  };

  const handleChangeRowsPerPage = (event: React.ChangeEvent<HTMLInputElement>) => {
    onPageChange(1, parseInt(event.target.value, 10));
  };

  const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('pt-BR', {
      style: 'currency',
      currency: 'BRL',
    }).format(value);
  };

  return (
    <Paper sx={{ width: '100%', overflow: 'hidden' }}>
      <TableContainer>
        <Table>
          <TableHead>
            <TableRow>
              <TableCell>ID</TableCell>
              <TableCell>Nome</TableCell>
              <TableCell>Descrição</TableCell>
              <TableCell align="right">Preço</TableCell>
              <TableCell align="right">Estoque</TableCell>
              <TableCell>Status</TableCell>
              <TableCell align="center">Ações</TableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {loading ? (
              <TableRow>
                <TableCell colSpan={7} align="center">
                  Carregando...
                </TableCell>
              </TableRow>
            ) : products.length === 0 ? (
              <TableRow>
                <TableCell colSpan={7} align="center">
                  Nenhum produto encontrado
                </TableCell>
              </TableRow>
            ) : (
              products.map((product) => (
                <TableRow key={product.id} hover>
                  <TableCell>{product.id}</TableCell>
                  <TableCell>{product.name}</TableCell>
                  <TableCell>{product.description}</TableCell>
                  <TableCell align="right">
                    {formatCurrency(product.price)}
                  </TableCell>
                  <TableCell align="right">{product.stock}</TableCell>
                  <TableCell>
                    {product.stock > 0 ? (
                      <Chip label="Em estoque" color="success" size="small" />
                    ) : (
                      <Chip label="Esgotado" color="error" size="small" />
                    )}
                  </TableCell>
                  <TableCell align="center">
                    <IconButton
                      size="small"
                      color="primary"
                      onClick={() => onEdit(product)}
                    >
                      <EditIcon />
                    </IconButton>
                    <IconButton
                      size="small"
                      color="error"
                      onClick={() => onDelete(product.id)}
                    >
                      <DeleteIcon />
                    </IconButton>
                  </TableCell>
                </TableRow>
              ))
            )}
          </TableBody>
        </Table>
      </TableContainer>
      <TablePagination
        component="div"
        count={total}
        page={page - 1}
        onPageChange={handleChangePage}
        rowsPerPage={limit}
        onRowsPerPageChange={handleChangeRowsPerPage}
        rowsPerPageOptions={[5, 10, 25, 50]}
        labelRowsPerPage="Itens por página:"
        labelDisplayedRows={({ from, to, count }) =>
          `${from}-${to} de ${count}`
        }
      />
    </Paper>
  );
};

export default ProductsTable;
```

## Integrações

### Integrar com Aplicação Externa (JavaScript)

```javascript
// auth.js - Módulo de autenticação
const API_BASE_URL = 'http://localhost:5209/api';

class AdminPanelAPI {
  constructor() {
    this.token = localStorage.getItem('admin_token');
  }

  async login(identifier, password) {
    const response = await fetch(`${API_BASE_URL}/auth/login`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ identifier, password }),
    });

    if (!response.ok) {
      throw new Error('Login falhou');
    }

    const data = await response.json();
    this.token = data.token;
    localStorage.setItem('admin_token', data.token);
    return data;
  }

  async getUsers(page = 1, limit = 10) {
    const response = await fetch(
      `${API_BASE_URL}/users?page=${page}&limit=${limit}`,
      {
        headers: {
          'Authorization': `Bearer ${this.token}`,
        },
      }
    );

    if (!response.ok) {
      throw new Error('Erro ao buscar usuários');
    }

    return await response.json();
  }

  async createUser(userData) {
    const response = await fetch(`${API_BASE_URL}/users`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${this.token}`,
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(userData),
    });

    if (!response.ok) {
      throw new Error('Erro ao criar usuário');
    }

    return await response.json();
  }
}

// Uso
const api = new AdminPanelAPI();

// Login
await api.login('root', 'root1234');

// Buscar usuários
const users = await api.getUsers(1, 10);
console.log(users);

// Criar usuário
const newUser = await api.createUser({
  username: 'joao',
  email: 'joao@example.com',
  password: 'senha123',
  fullName: 'João da Silva',
  permissionsIds: [2],
});
```

### Webhook para Sincronização

```csharp
// Backend - Controller para receber webhooks
[ApiController]
[Route("api/webhooks")]
public class WebhooksController : ControllerBase
{
    private readonly SyncExternalUsers _syncService;

    public WebhooksController(SyncExternalUsers syncService)
    {
        _syncService = syncService;
    }

    [HttpPost("user-created")]
    public async Task<ActionResult> UserCreated([FromBody] ExternalUserDto dto)
    {
        // Valida assinatura do webhook (implementar)
        if (!ValidateWebhookSignature(Request))
        {
            return Unauthorized();
        }

        await _syncService.ExecuteAsync(dto);
        return Ok();
    }

    private bool ValidateWebhookSignature(HttpRequest request)
    {
        // Implementar validação de assinatura
        return true;
    }
}
```

## Mais Exemplos

Para mais exemplos, consulte:
- [Guia de Desenvolvimento](./08-DESENVOLVIMENTO.md) - Como estender o sistema
- [API Reference](./05-API-REFERENCE.md) - Documentação completa dos endpoints
- Código fonte em `Api/Services/` e `WebApp/src/`
