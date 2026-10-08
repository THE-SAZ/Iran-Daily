/**
 * Iran-Daily — Lightweight Reactive Store
 *
 * A minimal observable store with typed setters.
 * No external dependencies.
 *
 * @author THE SAZ <https://github.com/THE-SAZ>
 */

type Subscriber<T> = (value: T) => void;

export class Store<T extends Record<string, unknown>> {
  private state: T;
  private subscribers = new Set<Subscriber<T>>();

  constructor(initial: T) {
    this.state = { ...initial };
  }

  get<K extends keyof T>(key: K): T[K] {
    return this.state[key];
  }

  set<K extends keyof T>(key: K, value: T[K]): void {
    if (this.state[key] === value) return;
    this.state = { ...this.state, [key]: value };
    this.notify();
  }

  patch(partial: Partial<T>): void {
    this.state = { ...this.state, ...partial };
    this.notify();
  }

  subscribe(fn: Subscriber<T>): () => void {
    this.subscribers.add(fn);
    fn(this.state);
    return () => this.subscribers.delete(fn);
  }

  private notify(): void {
    for (const fn of this.subscribers) {
      try { fn(this.state); } catch { /* ignore */ }
    }
  }
}
