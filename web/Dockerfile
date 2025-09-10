FROM node:20-alpine
WORKDIR /app

# Dependencies first for better caching
COPY package.json package-lock.json* pnpm-lock.yaml* yarn.lock* .npmrc* ./
RUN if [ -f pnpm-lock.yaml ]; then npm i -g pnpm@9 && pnpm i --frozen-lockfile; \
    elif [ -f yarn.lock ]; then yarn install --frozen-lockfile; \
    elif [ -f package-lock.json ]; then npm ci; \
    else npm i; fi

# App source
COPY . .

ENV HOST=0.0.0.0 PORT=5173
EXPOSE 5173

CMD ["npm", "run", "dev", "--", "--host"]


